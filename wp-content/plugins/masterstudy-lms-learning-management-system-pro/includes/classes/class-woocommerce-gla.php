<?php
use Automattic\WooCommerce\GoogleListingsAndAds\Product\ProductHelper;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Plugin\Taxonomy;
use Automattic\WooCommerce\GoogleListingsAndAds\Product\SyncerHooks;

class STM_LMS_GLA_Course_Integration {

	private array $post_types = array();
	private $container        = null;

	public function __construct() {
		$this->post_types[] = PostType::COURSE;

		if ( is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			$this->post_types[] = PostType::COURSE_BUNDLES;
		}

		add_filter( 'woocommerce_gla_product_query_args', array( $this, 'product_query_args' ) );
		add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'extend_product_query_post_types' ), 10, 2 );

		foreach ( $this->post_types as $post_type ) {
			add_action( "save_post_{$post_type}", array( $this, 'sync_course_with_gla' ), 99 );
		}

		add_action( 'masterstudy_lms_course_price_updated', array( $this, 'sync_course_with_gla' ) );
		add_action( 'masterstudy_lms_course_saved', array( $this, 'sync_course_with_gla' ) );
		add_action( 'before_delete_post', array( $this, 'delete_course_from_gla' ), 90 );
		add_action( 'trashed_post', array( $this, 'delete_course_from_gla' ), 90 );
		add_action( 'untrashed_post', array( $this, 'restore_course_in_gla' ), 90 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 20 );
		add_action( 'save_post_' . PostType::COURSE, array( $this, 'save_meta_visibility' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts_styles' ) );
	}

	/**
	 * Initialize the container if not already initialized.
	 *
	 * @return object|null
	 */
	private function get_container() {
		if ( is_null( $this->container ) && function_exists( 'woogle_get_container' ) ) {
			$this->container = woogle_get_container();
		}

		return $this->container;
	}

	/**
	 * Adds a query parameter to filter GLA products.
	 *
	 * @param array $args The original query arguments.
	 *
	 * @return array The modified query arguments with the added filter.
	 */
	public function product_query_args( array $args ): array {
		$args['stm_query_source'] = true;
		return $args;
	}

	/**
	 * Extends the product query to include courses and bundles.
	 *
	 * @param array $query The query array.
	 * @param array $query_vars The query variables.
	 * @return array The extended query array with the additional post types and taxonomies.
	 */
	public function extend_product_query_post_types( array $query, array $query_vars ): array {
		if ( empty( $query_vars['stm_query_source'] ) ) {
			return $query;
		}

		$query['post_type'] = array_merge( array( 'product' ), $this->post_types );
		$existing_tax_query = $query['tax_query'] ?? array();

		$query['tax_query'] = array(
			'relation' => 'OR',
			...$existing_tax_query,
			array(
				'taxonomy' => Taxonomy::COURSE_CATEGORY,
				'field'    => 'term_id',
				'operator' => 'EXISTS',
			),
		);

		return $query;
	}

	/**
	 * Syncs the course with Google Listings and Ads when the course is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function sync_course_with_gla( int $post_id ) {
		if ( $this->is_skip_sync( $post_id ) ) {
			return;
		}

		$container = $this->get_container();
		if ( ! $container ) {
			return;
		}

		try {
			/** @var ProductHelper $product_helper */
			$product_helper = $container->get( ProductHelper::class );
			$product        = wc_get_product( $post_id );

			if ( $product_helper->get_validation_errors( $product ) ) {
				$validation_errors = $product_helper->get_validation_errors( $product );
				do_action( 'masterstudy_gla_exception', implode( ', ', $validation_errors ), __METHOD__ );

				/** @var Automattic\WooCommerce\GoogleListingsAndAds\Product\ProductMetaHandler $product_meta_handler */
				$product_meta_handler = $container->get( Automattic\WooCommerce\GoogleListingsAndAds\Product\ProductMetaHandler::class );
				$product_meta_handler->delete_errors( $product );
			}

			$this->process_sync( $post_id );
		} catch ( Exception $exception ) {
			do_action( 'masterstudy_gla_exception', $exception, __METHOD__ );
		}
	}

	/**
	 * Checks if syncing should be skipped for a given post.
	 *
	 * @param int $post_id The ID of the post to check.
	 *
	 * @return bool True if syncing should be skipped, false otherwise.
	 */
	private function is_skip_sync( int $post_id ): bool {
		return defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || wp_is_post_revision( $post_id );
	}

	/**
	 * Handles course synchronization with GLA.
	 *
	 * @param int $post_id The ID of the post being synced.
	 */
	private function process_sync( int $post_id ) {
		try {
			$product = wc_get_product( $post_id );

			if ( ! $product instanceof WC_Product ) {
				return;
			}

			$container = $this->get_container();
			if ( ! $container ) {
				return;
			}

			/** @var SyncerHooks $syncer */
			$syncer = $container->get( SyncerHooks::class );
			$syncer->update_by_object( $post_id, $product );
		} catch ( Exception $exception ) {
			do_action( 'masterstudy_gla_exception', $exception, __METHOD__ );
		}
	}

	/**
	 * Deletes a course from GLA when the course is deleted.
	 *
	 * @param int $post_id The ID of the post being deleted.
	 */
	public function delete_course_from_gla( int $post_id ) {
		$container = $this->get_container();
		if ( ! $container ) {
			return;
		}

		try {
			/** @var SyncerHooks $syncer */
			$syncer = $container->get( SyncerHooks::class );
			$syncer->pre_delete( $post_id );
			$syncer->delete( $post_id );
		} catch ( Exception $exception ) {
			do_action( 'masterstudy_gla_exception', $exception, __METHOD__ );
		}
	}

	/**
	 * Restores a course in GLA when it is restored from the trash.
	 *
	 * @param int $post_id The ID of the post being restored.
	 */
	public function restore_course_in_gla( int $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post || ! in_array( $post->post_type, $this->post_types, true ) || 'publish' !== $post->post_status ) {
			return;
		}

		$this->sync_course_with_gla( $post_id );
	}

	/**
	 * Adds the meta box for visibility settings on the product and course edit pages.
	 */
	public function add_meta_box() {
		global $wp_meta_boxes;

		if ( isset( $wp_meta_boxes['product']['side']['default']['channel_visibility'] ) ) {
			$gla_meta_box = $wp_meta_boxes['product']['side']['default']['channel_visibility'];

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_meta_boxes[ PostType::COURSE ]['side']['default']['channel_visibility'] = $gla_meta_box;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_meta_boxes[ PostType::COURSE_BUNDLES ]['side']['default']['channel_visibility'] = $gla_meta_box;

			wp_enqueue_style( 'masterstudy-admin-course-visibility' );
		}
	}

	/**
	 * Saves the visibility metadata for courses.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_meta_visibility( int $post_id ) {
		$container = $this->get_container();
		if ( ! $container ) {
			return;
		}

		try {
			/** @var Automattic\WooCommerce\GoogleListingsAndAds\Admin\MetaBox\ChannelVisibilityMetaBox $channel_visibility */
			$channel_visibility = $container->get( Automattic\WooCommerce\GoogleListingsAndAds\Admin\MetaBox\ChannelVisibilityMetaBox::class );

			$_POST['woocommerce_meta_nonce'] = wp_create_nonce( 'woocommerce_save_data' );
			$channel_visibility->handle_submission( $post_id, wc_get_product( $post_id ) );
		} catch ( Exception $exception ) {
			do_action( 'masterstudy_gla_exception', $exception, __METHOD__ );
		}
	}

	public function add_admin_scripts_styles() {
		wp_register_style( 'masterstudy-admin-course-visibility', STM_LMS_PRO_URL . 'assets/css/admin/select-visibility-box.css', array(), STM_LMS_PRO_VERSION );
	}
}
