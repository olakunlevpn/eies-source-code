<?php
/**
 * @var $wishlist
 */

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;

$taxes_display = masterstudy_lms_taxes_display();
$bundles       = ( new CourseBundleRepository() )->get_bundles();

$wishlist     = $wishlist ?? array();
$bundles_list = ( ! empty( $bundles['posts'] ) ) ? $bundles['posts'] : array();
$courses      = ( ! empty( $bundles['courses'] ) ) ? $bundles['courses'] : array();
$total_pages  = ( ! empty( $bundles['pages'] ) ) ? $bundles['pages'] : 1;

wp_enqueue_style( 'masterstudy-account-bundles' );
wp_enqueue_script( 'masterstudy-account-bundles' );
wp_localize_script(
	'masterstudy-account-bundles',
	'bundles_data',
	array(
		'list'          => $bundles,
		'per_page'      => 6,
		'taxes_enabled' => $taxes_display['enabled'],
		'strings'       => array(
			'delete_confirm' => esc_html__( 'Do you really want to delete this bundle?', 'masterstudy-lms-learning-management-system-pro' ),
			'error'          => esc_html__( 'Error', 'masterstudy-lms-learning-management-system-pro' ),
			'move_to_drafts' => __( 'Move to drafts', 'masterstudy-lms-learning-management-system-pro' ),
			'publish'        => __( 'Publish', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);
?>

<div class="masterstudy-account-bundles">
	<div class="masterstudy-account-bundles__header">
		<h1 class="masterstudy-account-bundles__title">
			<?php echo esc_html__( 'Bundles', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</h1>
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'title'         => esc_html__( 'Add new bundle', 'masterstudy-lms-learning-management-system' ),
				'link'          => esc_url( ms_plugin_user_account_url( 'bundles/add_new' ) ),
				'style'         => 'secondary',
				'size'          => 'sm',
				'id'            => 'add_new_bundle',
				'icon_name'     => 'plus',
				'icon_position' => 'left',
			)
		);
		?>
	</div>

	<div class="masterstudy-account-bundles__list">
		<?php
		if ( ! empty( $bundles_list ) ) :
			foreach ( $bundles_list as $bundle ) :
				STM_LMS_Templates::show_lms_template(
					'bundle/card/main',
					array(
						'bundle'  => $bundle,
						'courses' => $courses,
					)
				);
			endforeach;
		endif;
		?>
	</div>

	<div class="masterstudy-account-bundles__pagination">
		<?php
		if ( ! empty( $bundles_list ) && $total_pages > 1 ) {
			STM_LMS_Templates::show_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $total_pages,
					'current_page'      => 1,
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
					'thin'              => true,
				)
			);
		}
		?>
	</div>

	<div class="masterstudy-account-bundles__loader">
		<div class="masterstudy-account-bundles__loader-body"></div>
	</div>

	<div class="masterstudy-account-bundles__empty <?php echo esc_attr( empty( $bundles_list ) ? 'masterstudy-account-bundles__empty_show' : '' ); ?>">
		<div class="masterstudy-account-bundles__empty-block">
			<span class="masterstudy-account-bundles__empty-icon"></span>
			<span class="masterstudy-account-bundles__empty-text">
				<?php echo esc_html__( 'No bundles yet', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
		</div>
	</div>
</div>
