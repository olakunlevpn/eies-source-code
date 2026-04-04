<?php
/**
 * @var int $bundle_id
 */

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;

wp_enqueue_style( 'masterstudy-bundle-button' );
wp_enqueue_script( 'masterstudy-bundle-button' );

$guest_checkout       = STM_LMS_Options::get_option( 'guest_checkout', false );
$bundle_price         = CourseBundleRepository::get_bundle_price( $bundle_id );
$bundle_courses_price = CourseBundleRepository::get_bundle_courses_price( $bundle_id );
$points_price         = CourseBundleRepository::get_bundle_points_price( $bundle_id );
$is_logged            = is_user_logged_in();

$dropdown_enabled = ! empty( $points_price ) && is_ms_lms_addon_enabled( 'point_system' );

$button_classes = array( 'masterstudy-bundle-button' );
$button_classes = array(
	implode( ' ', $button_classes ),
	( $dropdown_enabled ) ? 'masterstudy-bundle-button_dropdown' : '',
);

wp_localize_script(
	'masterstudy-bundle-button',
	'bundle_data',
	array(
		'guest_checkout' => $guest_checkout && ! $is_logged,
		'guest_nonce'    => wp_create_nonce( 'stm_lms_add_to_cart_guest' ),
		'nonce'          => wp_create_nonce( 'stm_lms_add_bundle_to_cart' ),
	)
);
?>

<div class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>">
	<a
		href="#"
		class="masterstudy-bundle-button__link <?php echo $is_logged || ( ! $is_logged && $guest_checkout ) ? 'masterstudy-bundle-button__link_active' : ''; ?>"
		<?php echo $is_logged ? 'data-purchase-bundle="' . intval( $bundle_id ) . '"' : ( esc_html( $guest_checkout ) ? 'data-guest="' . intval( $bundle_id ) . '"' : 'data-authorization-modal="login"' ); ?>
	>
		<span class="masterstudy-bundle-button__title">
			<?php esc_html_e( 'Get now', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</span>
		<?php if ( ! empty( $bundle_courses_price ) || ! empty( $bundle_price ) ) { ?>
			<span class="masterstudy-bundle-button__separator"></span>
			<div class="masterstudy-bundle-button__price">
				<?php if ( ! empty( $bundle_price ) ) { ?>
					<span class="masterstudy-bundle-button__price-bundle">
						<?php echo esc_html( masterstudy_lms_display_price_with_taxes( $bundle_price ) ); ?>
					</span>
					<?php
				}
				?>
			</div>
		<?php } ?>
	</a>
	<?php if ( $dropdown_enabled ) { ?>
		<div class="masterstudy-bundle-button_plans-dropdown">
			<?php if ( ! empty( $bundle_price ) ) { ?>
				<a href="#" <?php echo $is_logged ? 'data-purchase-bundle="' . intval( $bundle_id ) . '"' : ( esc_html( $guest_checkout ) ? 'data-guest="' . intval( $bundle_id ) . '"' : 'data-authorization-modal="login"' ); ?>>
					<span class="masterstudy-bundle-button__title"><?php esc_html_e( 'One Time Payment', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				</a>
				<?php
			}
			if ( $is_logged ) {
				STM_LMS_Templates::show_lms_template(
					'components/bundle-button/points',
					array(
						'bundle_id' => (int) $bundle_id,
					)
				);
			}
			?>
		</div>
	<?php } ?>
</div>
