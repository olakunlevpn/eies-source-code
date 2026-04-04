<?php
stm_module_scripts( 'header_js', 'header_2' );

$user_account_icon = stm_option( 'header_user_account_switch', true );
$logged_id         = is_user_logged_in();
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">

			<div class="header_top">

				<div class="logo-unit">
					<?php get_template_part( 'partials/headers/parts/logo' ); ?>
				</div>


				<div class="center-unit">
					<?php get_template_part( 'partials/headers/parts/center' ); ?>
				</div>

				<div class="right-unit">
					<?php get_template_part( 'partials/headers/parts/right' ); ?>
				</div>

				<button type="button" class="navbar-toggle collapsed hidden-lg hidden-md stm_header_top_search "
						data-toggle="collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<div class="stm_header_top_toggler <?php echo ( ! $logged_id || empty( $user_account_icon ) ? 'mbc' : '' ); ?>">
					<?php
					if ( $logged_id && ! empty( $user_account_icon ) ) {
						$user_id = get_current_user_id();
						$avatar  = get_user_meta( $user_id, 'stm_lms_user_avatar', true );

						if ( empty( $avatar ) ) {
							$avatar = get_avatar_url( $user_id, 32 );
						}
					}

					if ( ! empty( $avatar ) ) {
						?>
						<img src="<?php echo esc_html( $avatar ); ?>" class="avatar avatar-32 photo" alt="Avatar">
					<?php } else { ?>
						<i class="stmlms-user"></i>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$cats = stm_option( 'header_course_categories_online', array() );
if ( ! empty( $cats ) ) :
	?>

	<div class="categories-courses">
		<?php get_template_part( 'partials/headers/parts/courses_categories_with_search' ); ?>
	</div>

<?php endif; ?>


<?php get_template_part( 'partials/headers/mobile/account' ); ?>
<?php get_template_part( 'partials/headers/mobile/search' ); ?>
<?php get_template_part( 'partials/headers/mobile/menu' ); ?>
