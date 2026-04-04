<?php

use MasterStudy\Lms\Plugin\PostType;
use WooCommerce\Facebook\Admin as FacebookAdmin;
use WooCommerce\Facebook\Products;

class STM_LMS_FB_Commerce_Course_Integration {

	private array $post_types = array();

	public function __construct() {
		$this->post_types[] = PostType::COURSE;

		if ( is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			$this->post_types[] = PostType::COURSE_BUNDLES;
		}

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts_styles' ) );
		add_action( 'pre_get_posts', array( $this, 'add_query_args_sync' ) );

		foreach ( $this->post_types as $post_type ) {
			add_action( "save_post_{$post_type}", array( $this, 'sync_course_with_fb' ), 99 );
		}

		add_action( 'masterstudy_lms_course_price_updated', array( $this, 'sync_course_with_fb' ) );
		add_action( 'masterstudy_lms_course_saved', array( $this, 'sync_course_with_fb' ) );
		add_action( 'stm_lms_saved_bundle', array( $this, 'sync_course_with_fb' ) );

		add_action( 'before_delete_post', array( $this, 'delete_course_from_fb' ), 90 );
		add_action( 'trashed_post', array( $this, 'delete_course_from_fb' ), 90 );
		add_action( 'untrashed_post', array( $this, 'restore_course_in_fb' ), 90 );
		add_action( 'publish_to_draft', array( $this, 'delete_draft_course' ) );
	}

	/**
	 * Add Facebook visibility meta box to course edit screen.
	 */
	public function add_meta_box(): void {
		global $wp_meta_boxes;

		if ( isset( $wp_meta_boxes['product']['side']['default']['facebook_metabox'] ) ) {
			$gla_meta_box = $wp_meta_boxes['product']['side']['default']['facebook_metabox'];

			$gla_meta_box['callback'] = function ( WP_Post $post ) use ( $gla_meta_box ) {
				$this->box_output( $post );
				call_user_func( $gla_meta_box['callback'], $post );
			};

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_meta_boxes[ PostType::COURSE ]['side']['default']['facebook_metabox'] = $gla_meta_box;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_meta_boxes[ PostType::COURSE_BUNDLES ]['side']['default']['facebook_metabox'] = $gla_meta_box;

			wp_enqueue_style( 'masterstudy-admin-course-visibility' );
		}
	}

	/**
	 * Render Facebook visibility select box.
	 *
	 * @param WP_Post $post
	 */
	public function box_output( WP_Post $post ): void {
		$sync_enabled = get_post_meta( $post->ID, Products::SYNC_ENABLED_META_KEY, true ) !== 'no';
		$visibility   = get_post_meta( $post->ID, Products::VISIBILITY_META_KEY, true );
		$is_visible   = ! $visibility || wc_string_to_bool( $visibility );

		$sync_mode = $sync_enabled
			? ( $is_visible ? FacebookAdmin::SYNC_MODE_SYNC_AND_SHOW : FacebookAdmin::SYNC_MODE_SYNC_AND_HIDE )
			: FacebookAdmin::SYNC_MODE_SYNC_DISABLED;
		?>
		<div class="fb-commerce-visibility-box">
			<?php
				woocommerce_wp_select(
					array(
						'id'            => 'wc_facebook_sync_mode',
						'label'         => __( 'Facebook for WooCommerce', 'masterstudy-lms-learning-management-system-pro' ),
						'options'       => array(
							FacebookAdmin::SYNC_MODE_SYNC_AND_SHOW => __( 'Sync and show in catalog', 'masterstudy-lms-learning-management-system-pro' ),
							FacebookAdmin::SYNC_MODE_SYNC_AND_HIDE => __( 'Sync and hide in catalog', 'masterstudy-lms-learning-management-system-pro' ),
							FacebookAdmin::SYNC_MODE_SYNC_DISABLED => __( 'Do not sync', 'masterstudy-lms-learning-management-system-pro' ),
						),
						'value'         => $sync_mode,
						'description'   => __( 'Choose whether to sync this course to Facebook and its visibility in the catalog.', 'masterstudy-lms-learning-management-system-pro' ),
						'wrapper_class' => 'form-row form-row-full',
					)
				);
			?>
		</div>
		<?php
	}

	/**
	 * Register admin styles.
	 */
	public function add_admin_scripts_styles(): void {
		wp_register_style(
			'masterstudy-admin-course-visibility',
			STM_LMS_PRO_URL . 'assets/css/admin/select-visibility-box.css',
			array(),
			STM_LMS_PRO_VERSION
		);
	}

	/**
	 * Extend Facebook feed query to include LMS courses.
	 *
	 * @param WP_Query $query
	 */
	public function add_query_args_sync( WP_Query $query ): void {
		if ( doing_action( 'wc_facebook_regenerate_feed' ) ) {
			$post_type = (array) $query->get( 'post_type' );
			if ( in_array( 'product', $post_type, true ) ) {
				$query->set( 'post_type', array_merge( $post_type, $this->post_types ) );
			}
		}
	}

	/**
	 * Sync the course with Facebook catalog.
	 *
	 * @param int $post_id The post ID of the course.
	 */
	public function sync_course_with_fb( int $post_id ): void {
		$post_type = get_post_type( $post_id );
		if ( ! in_array( $post_type, $this->post_types, true ) ) {
			return;
		}

		$integration = $this->get_integration();
		if ( ! $integration || ! $integration->is_configured() || ! $integration->get_product_catalog_id() ) {
			return;
		}

		if ( doing_action( 'masterstudy_lms_course_price_updated' ) || doing_action( 'masterstudy_lms_course_saved' ) ) {
			$_POST['wc_facebook_sync_mode'] = get_post_meta( $post_id, Products::VISIBILITY_META_KEY, true );
		}

		if ( PostType::COURSE_BUNDLES === $post_type ) {
			$_POST['wc_facebook_sync_mode'] = get_post_meta( $post_id, Products::VISIBILITY_META_KEY, true );
		}

		$integration->on_product_save( $post_id );
	}

	/**
	 * Remove the course from Facebook catalog.
	 *
	 * @param int $post_id The post ID of the course.
	 */
	public function delete_course_from_fb( int $post_id ): void {
		if ( ! in_array( get_post_type( $post_id ), $this->post_types, true ) ) {
			return;
		}

		$integration = $this->get_integration();
		if ( $integration ) {
			$integration->on_product_delete( $post_id );
		}
	}

	/**
	 * Restore a previously trashed course in the Facebook catalog.
	 *
	 * @param int $post_id The post ID of the course.
	 */
	public function restore_course_in_fb( int $post_id ): void {
		if ( ! in_array( get_post_type( $post_id ), $this->post_types, true ) ) {
			return;
		}

		$product = wc_get_product( $post_id );
		if ( ! $product || ! $product->is_visible() ) {
			return;
		}

		$integration = $this->get_integration();
		if ( $integration ) {
			$integration->on_product_publish( $product->get_id() );
		}
	}

	/**
	 * Delete the course from Facebook when moved to draft.
	 *
	 * @param WP_Post $post
	 */
	public function delete_draft_course( WP_Post $post ): void {
		if ( in_array( $post->post_type, $this->post_types, true ) ) {
			$integration = $this->get_integration();
			if ( $integration ) {
				$integration->delete_draft_product( $post );
			}
		}
	}

	/**
	 * Get the Facebook commerce integration instance.
	 *
	 * @return WC_Facebookcommerce_Integration|null
	 */
	private function get_integration(): ?WC_Facebookcommerce_Integration {
		if ( ! class_exists( 'WC_Facebookcommerce_Integration' ) || ! class_exists( 'WC_Facebookcommerce' ) ) {
			return null;
		}

		return new WC_Facebookcommerce_Integration( new WC_Facebookcommerce() );
	}
}
