<?php
get_header();

$blog_sidebar_id       = stm_option( 'events_sidebar' );
$blog_sidebar_position = stm_option( 'events_sidebar_position', 'none' );
$content_before        = '';
$content_after         = '';
$sidebar_before        = '';
$sidebar_after         = '';
$blog_sidebar          = null;

if ( ! empty( $_GET['sidebar_position'] ) && 'right' == $_GET['sidebar_position'] ) {
	$blog_sidebar_position = 'right';
} elseif ( ! empty( $_GET['sidebar_position'] ) && 'left' == $_GET['sidebar_position'] ) {
	$blog_sidebar_position = 'left';
} elseif ( ! empty( $_GET['sidebar_position'] ) && 'none' == $_GET['sidebar_position'] ) {
	$blog_sidebar_position = 'none';
}

if ( $blog_sidebar_id ) {
	$blog_sidebar = get_post( $blog_sidebar_id );
}

$can_read_sidebar = false;
if ( $blog_sidebar instanceof WP_Post ) {
	$blog_status      = get_post_status( $blog_sidebar );
	$no_pass          = ! post_password_required( $blog_sidebar );
	$can_read_sidebar = $no_pass && (
		'publish' === $blog_status
		|| ( is_user_logged_in() && current_user_can( 'read_post', $blog_sidebar->ID ) )
	);
}

if ( 'right' == $blog_sidebar_position && isset( $blog_sidebar ) ) {
	$content_before .= '<div class="row">';
	$content_before .= '<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">';
	$content_after  .= '</div>';
	$sidebar_before .= '<div class="col-lg-3 col-md-3 hidden-sm hidden-xs">';
	$sidebar_after  .= '</div>';
	$sidebar_after  .= '</div>';
}

if ( 'left' == $blog_sidebar_position && isset( $blog_sidebar ) ) {
	$content_before .= '<div class="row">';
	$content_before .= '<div class="col-lg-9 col-lg-push-3 col-md-9 col-md-push-3 col-sm-12 col-xs-12">';
	$content_after  .= '</div>';
	$sidebar_before .= '<div class="col-lg-3 col-lg-pull-9 col-md-3 col-md-pull-9 hidden-sm hidden-xs">';
	$sidebar_after  .= '</div>';
	$sidebar_after  .= '</div>';
}

get_template_part( 'partials/title_box' );
?>

<div class="container">
	<?php
	if ( have_posts() ) :
		?>
		<?php echo wp_kses_post( $content_before ); ?>
			<div class="<?php echo esc_attr( 'sidebar_position_' . $blog_sidebar_position ); ?>">
				<div class="row">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'partials/loop', 'events' );
					endwhile;
				?>
				</div>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo paginate_links(
					array(
						'type'      => 'list',
						'prev_text' => '<i class="fa fa-chevron-left"></i><span class="pagi_label">' . __( 'Previous', 'masterstudy' ) . '</span>',
						'next_text' => '<span class="pagi_label">' . __( 'Next', 'masterstudy' ) . '</span><i class="fa fa-chevron-right"></i>',
					)
				);
				?>
			</div> <!-- blog_layout -->
		<?php echo wp_kses_post( $content_after ); ?>
		<?php echo wp_kses_post( $sidebar_before ); ?>
			<div class="sidebar-area sidebar-area-<?php echo esc_attr( $blog_sidebar_position ); ?>">
				<?php
				if ( isset( $blog_sidebar ) && 'none' != $blog_sidebar_position ) {
					if ( $can_read_sidebar ) {
						echo wp_kses_post( $blog_sidebar->post_content );
					}
				}
				?>
			</div>
		<?php echo wp_kses_post( $sidebar_after ); ?>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
