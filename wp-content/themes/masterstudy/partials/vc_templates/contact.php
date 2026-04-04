<?php
/**
 * @var $style
 * @var $name
 * @var $image
 * @var $job
 * @var $phone
 * @var $email
 * @var $skype
 * @var $image_id
 * @var $css_class
 */

stm_module_styles( 'contact', 'style_1', array() );

$image_size = ( ! empty( $image_size ) ) ? $image_size : 'thumbnail';
$name       = $name ?? '';
$job        = $job ?? '';
$phone      = $phone ?? '';
$email      = $email ?? '';
$skype      = $skype ?? '';
?>

<div class="stm_contact<?php echo esc_attr( $css_class ); ?> clearfix">
	<?php if ( ! empty( $image['thumbnail'] ) ) : ?>
		<div class="stm_contact_image">
			<?php echo wp_kses_post( stm_get_VC_img( $image_id, $image_size ) ); ?>
		</div>
	<?php endif; ?>
	<div class="stm_contact_info">
		<?php if ( ! empty( $name ) ) : ?>
			<h4 class="name">
				<?php echo esc_html( $name ); ?>
			</h4>
		<?php endif; ?>
		<?php if ( ! empty( $job ) ) : ?>
			<div class="stm_contact_job heading_font">
				<?php echo esc_html( $job ); ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $phone ) ) : ?>
			<div class="stm_contact_row">
				<?php printf( esc_html__( 'Phone: ', 'masterstudy' ) . '%s', esc_html( $phone ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $email ) ) : ?>
			<div class="stm_contact_row">
				<?php esc_html_e( 'Email: ', 'masterstudy' ); ?>
				<a href="mailto:<?php echo esc_attr( $email ); ?>">
					<?php echo esc_html( $email ); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $skype ) ) : ?>
			<div class="stm_contact_row">
				<?php esc_html_e( 'Skype: ', 'masterstudy' ); ?>
				<a href="skype:<?php echo esc_attr( $skype ); ?>?chat">
					<?php echo esc_html( $skype ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
