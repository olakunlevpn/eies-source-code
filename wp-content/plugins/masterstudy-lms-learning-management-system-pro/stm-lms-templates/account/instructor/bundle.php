<?php
/**
 * @var int $bundle_id
 */

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;
use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleSettings;

if ( ! is_user_logged_in() ) {
	STM_LMS_User::js_redirect( STM_LMS_User::login_page_url() );
	die;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-account-main' );

$bundle_id      = ! empty( $bundle_id ) ? intval( $bundle_id ) : '';
$bundle_data    = ! empty( $bundle_id ) ? ( new CourseBundleRepository() )->get_bundle_data( $bundle_id ) : null;
$bundle_content = $bundle_data->post_content ?? '';
$page_title     = ! empty( $bundle_data ) ? esc_html__( 'Edit bundle', 'masterstudy-lms-learning-management-system-pro' ) : esc_html__( 'New bundle', 'masterstudy-lms-learning-management-system-pro' );
$limit          = ( new CourseBundleSettings() )->get_bundle_courses_limit();
$is_full        = ! empty( $bundle_data->bundle_courses ) ? count( $bundle_data->bundle_courses ) === (int) $limit : false;

wp_enqueue_style( 'masterstudy-add-bundle' );
wp_enqueue_script( 'masterstudy-add-bundle' );
wp_localize_script(
	'masterstudy-add-bundle',
	'bundle_courses',
	array(
		'limit'              => $limit,
		'nonce'              => wp_create_nonce( 'stm_lms_save_bundle' ),
		'my_courses'         => ! empty( $bundle_data->bundle_courses ) ? wp_list_pluck( $bundle_data->bundle_courses, 'id' ) : array(),
		'my_bundle_id'       => ! empty( $bundle_data->ID ) ? $bundle_data->ID : 0,
		'currency_symbol'    => STM_LMS_Options::get_option( 'currency_symbol', '$' ),
		'currency_position'  => STM_LMS_Options::get_option( 'currency_position', 'left' ),
		'currency_thousands' => STM_LMS_Options::get_option( 'currency_thousands', ',' ),
		'currency_decimals'  => STM_LMS_Options::get_option( 'currency_decimals', '.' ),
		'decimals_num'       => STM_LMS_Options::get_option( 'decimals_num', '2' ),
	)
);
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-add-bundle">
			<div class="masterstudy-add-bundle__header">
				<h2 class="masterstudy-add-bundle__title">
					<?php echo esc_html( $page_title ); ?>
				</h2>
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => __( 'Save', 'masterstudy-lms-learning-management-system-pro' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
						'id'    => 'save_bundle',
					)
				);
				?>
			</div>
			<div class="masterstudy-add-bundle__content">
				<span class="masterstudy-add-bundle__content-title">
					<?php echo esc_html__( 'Bundle details', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
				<div class="masterstudy-add-bundle__block">
					<span class="masterstudy-add-bundle__label">
						<?php echo esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ); ?>
						<span class="masterstudy-add-bundle__required">*</span>
					</span>
					<input type="text" name="bundle_title" class="masterstudy-add-bundle__input" value="<?php echo isset( $bundle_data->post_title ) ? esc_attr( $bundle_data->post_title ) : ''; ?>">
				</div>
				<div class="masterstudy-add-bundle__block">
					<span class="masterstudy-add-bundle__label">
						<?php echo esc_html__( 'Preview image', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/image-upload',
						array(
							'id'                 => 'bundle_image',
							'attachment'         => isset( $bundle_data->bundle_image ) ? $bundle_data->bundle_image : null,
							'allowed_extensions' => array( '.png', '.jpg', '.jpeg' ),
							'allowed_filesize'   => 10,
						)
					);
					?>
				</div>
				<div class="masterstudy-add-bundle__block">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/wp-editor',
						array(
							'id'        => 'editor_add_bundle',
							'content'   => $bundle_content,
							'words_off' => true,
							'settings'  => array(
								'quicktags'     => false,
								'media_buttons' => false,
								'textarea_rows' => 13,
							),
						)
					);
					?>
				</div>
				<div class="masterstudy-add-bundle__block masterstudy-add-bundle__block_pricing">
					<span class="masterstudy-add-bundle__block-title">
						<?php echo esc_html__( 'Pricing', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/pricing/main',
						array(
							'points_show'      => true,
							'points_info'      => '',
							'points_info_show' => false,
							'points'           => ! empty( $bundle_data->points ) ? $bundle_data->points : null,
							'price_info'       => '',
							'price_info_show'  => false,
							'price'            => ! empty( $bundle_data->bundle_price ) ? $bundle_data->bundle_price : null,
							'bundle_id'        => $bundle_id,
						)
					);
					?>
				</div>
				<div class="masterstudy-add-bundle__products">
					<span class="masterstudy-add-bundle__products-title">
						<?php echo esc_html__( 'Target products', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
					<div class="masterstudy-add-bundle__courses">
						<?php
						if ( ! empty( $bundle_data->bundle_courses ) ) {
							foreach ( $bundle_data->bundle_courses as $course ) {
								STM_LMS_Templates::show_lms_template(
									'components/bundle/course-card',
									array(
										'course'     => $course,
										'selectable' => false,
										'removable'  => true,
									)
								);
							}
						} else {
							?>
							<div class="masterstudy-add-bundle__courses-empty">
								<?php echo esc_html__( 'No courses selected yet', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</div>
						<?php } ?>
					</div>
					<div class="masterstudy-add-bundle__courses-add-wrapper">
						<div class="masterstudy-add-bundle__courses-add <?php echo $is_full ? 'masterstudy-add-bundle__courses-add_hide' : ''; ?>">
							<?php
							STM_LMS_Templates::show_lms_template(
								'components/button',
								array(
									'title'         => __( 'Add', 'masterstudy-lms-learning-management-system-pro' ),
									'link'          => '#',
									'style'         => 'primary',
									'size'          => 'sm',
									'icon_name'     => 'plus',
									'icon_position' => 'left',
									'id'            => 'add_course_bundle',
								)
							);
							?>
						</div>
						<div class="masterstudy-add-bundle__courses-limit">
							<?php echo esc_html__( 'You can select up to', 'masterstudy-lms-learning-management-system-pro' ) . ' ' . intval( $limit ) . ' ' . esc_html__( 'courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
						</div>
					</div>
				</div>
				<div class="masterstudy-add-bundle__summary">
					<div class="masterstudy-add-bundle__summary-header">
						<div class="masterstudy-add-bundle__summary-title">
							<?php echo esc_html__( 'Total Price of Selected Items', 'masterstudy-lms-learning-management-system-pro' ); ?>:
						</div>
						<div class="masterstudy-add-bundle__summary-total">
							0
						</div>
					</div>
					<div class="masterstudy-add-bundle__summary-list">
						<div class="masterstudy-add-bundle__summary-item masterstudy-add-bundle__summary-item_one-time">
							<div class="masterstudy-add-bundle__summary-item__wrapper">
								<span class="masterstudy-add-bundle__summary-item-label">
									<?php echo esc_html__( 'One time purchase', 'masterstudy-lms-learning-management-system-pro' ); ?>:
								</span>
								<div class="masterstudy-add-bundle__summary-benefit">
									<span class="masterstudy-add-bundle__summary-benefit-label">
										<?php echo esc_html__( 'Benefit', 'masterstudy-lms-learning-management-system-pro' ); ?>:
									</span>
									<span class="masterstudy-add-bundle__summary-benefit-value"></span>
								</div>
							</div>
							<span class="masterstudy-add-bundle__summary-item-value"></span>
						</div>
						<div class="masterstudy-add-bundle__summary-item masterstudy-add-bundle__summary-item_points">
							<span class="masterstudy-add-bundle__summary-item-label">
								<?php echo esc_html__( 'Buy with points', 'masterstudy-lms-learning-management-system-pro' ); ?>:
							</span>
							<span class="masterstudy-add-bundle__summary-item-value"></span>
						</div>
					</div>
				</div>
				<div class="masterstudy-add-bundle__save">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => __( 'Save', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'primary',
							'size'  => 'sm',
							'id'    => 'save_bundle',
						)
					);
					?>
				</div>
				<div class="masterstudy-add-bundle__errors"></div>
			</div>
		</div>
	</div>
</div>
<?php
STM_LMS_Templates::show_lms_template( 'components/bundle/select-courses' );

do_action( 'masterstudy_after_account', $lms_current_user );
