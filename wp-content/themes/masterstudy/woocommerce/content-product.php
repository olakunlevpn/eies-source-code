<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$classes = array();

$shop_sidebar_position = stm_option( 'shop_sidebar_position', 'none' );
if ( isset( $_GET['sidebar_position'] ) && 'none' === $_GET['sidebar_position'] ) {
	$shop_sidebar_position = 'none';
}

if ( 'none' === $shop_sidebar_position ) {
	$classes[] = 'col-md-3 col-sm-4 col-xs-6 course-col';
} else {
	$classes[] = 'col-md-4 col-sm-4 col-xs-6 course-col';
}

$enable_shop = stm_option( 'enable_shop', false );

if ( $enable_shop ) : ?>
	<li <?php wc_product_class( $classes, $product ); ?>>
		<div class="product__inner">

			<?php
			/**
			 * Hook: woocommerce_before_shop_loop_item.
			 *
			 * @hooked woocommerce_template_loop_product_link_open - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item' );

			/**
			 * Hook: woocommerce_before_shop_loop_item_title.
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			?>
			<?php
			do_action( 'woocommerce_before_shop_loop_item_title' );

			/**
			 * Hook: woocommerce_shop_loop_item_title.
			 *
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			do_action( 'woocommerce_shop_loop_item_title' );

			/**
			 * Hook: woocommerce_after_shop_loop_item_title.
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );

			/**
			 * Hook: woocommerce_after_shop_loop_item.
			 *
			 * @hooked woocommerce_template_loop_product_link_close - 5
			 * @hooked woocommerce_template_loop_add_to_cart - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item' );
			?>
		</div>
	</li>
<?php else : ?>
	<!-- Custom Meta -->
	<?php
	$experts          = get_post_meta( get_the_id(), 'course_expert', true );
	$stock            = get_post_meta( get_the_id(), '_stock', true );
	$regular_price    = $product->get_regular_price();
	$sale_price       = $product->get_sale_price();
	$add_to_cart_text = property_exists( 'MasterStudy\Lms\Plugin\PostType', 'COURSE' ) ? esc_html__( 'View more', 'masterstudy' ) : $product->add_to_cart_text();
	?>
	<li <?php wc_product_class( $classes, $product ); ?>>
		<?php
		/**
		 * woocommerce_before_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item' );
		?>

		<div class="stm_archive_product_inner_unit heading_font">
			<div class="stm_archive_product_inner_unit_centered">
				<div class="stm_featured_product_image">
					<?php if ( $product->is_type( 'simple' ) ) { ?>
						<div class="stm_featured_product_price">
							<?php if ( ! empty( $sale_price ) ) : ?>
								<div class="price">
									<?php echo wp_kses_post( wc_price( $sale_price ) ); ?>
								</div>
							<?php elseif ( ! empty( $regular_price ) ) : ?>
								<div class="price">
									<h5><?php echo wp_kses_post( wc_price( $regular_price ) ); ?></h5>
								</div>
							<?php else : ?>
								<div class="price price_free">
									<h5><?php esc_html_e( 'Free', 'masterstudy' ); ?></h5>
								</div>
							<?php endif; ?>
						</div>
					<?php } elseif ( $product->is_type( 'variable' ) ) { ?>
						<?php $available_variations = $product->get_available_variations(); ?>
						<?php if ( ! empty( $available_variations[0]['display_regular_price'] ) ) : ?>
							<div class="stm_featured_product_price">
								<div class="price">
									<?php if ( ! empty( $available_variations[0]['display_price'] ) ) : ?>
										<?php echo wp_kses_post( wc_price( $available_variations[0]['display_price'] ) ); ?>
									<?php else : ?>
										<?php echo wp_kses_post( wc_price( $available_variations[0]['display_regular_price'] ) ); ?>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php } ?>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php esc_attr_e( 'View course', 'masterstudy' ); ?> - <?php echo esc_attr( $product->get_title() ); ?>">
							<?php echo wp_kses_post( $product->get_image( 'img-270-283', array( 'class' => 'img-responsive' ) ) ); ?>
						</a>
					<?php else : ?>
						<div class="no_image_holder"></div>
					<?php endif; ?>
				</div>

				<div class="stm_featured_product_body">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php esc_attr_e( 'View course', 'masterstudy' ); ?> - <?php echo esc_attr( $product->get_title() ); ?>">
						<div class="title"><?php echo esc_html( $product->get_title() ); ?></div>
					</a>
					<?php if ( ! empty( $experts ) && 'no_expert' !== $experts && ( is_array( $experts ) && ! in_array( 'no_expert', $experts, true ) ) ) : ?>
						<div class="expert">
							<?php
							foreach ( $experts as $expert ) {
								echo esc_html( get_the_title( $expert ) ) . ( end( $experts ) !== $expert ) ? ', ' : '';
							}
							?>
						</div>
					<?php else : ?>
						<div class="expert">&nbsp;</div>
					<?php endif; ?>
				</div>

				<div class="stm_featured_product_footer">
					<div class="clearfix">
						<div class="pull-left">
							<?php $comments_num = get_comments_number( get_the_id() ); ?>
							<?php if ( $comments_num ) : ?>
								<div class="stm_featured_product_comments">
									<i class="fa-icon-stm_icon_comment_o"></i><span><?php echo esc_attr( $comments_num ); ?></span>
								</div>
							<?php else : ?>
								<div class="stm_featured_product_comments">
									<i class="fa-icon-stm_icon_comment_o"></i><span>0</span>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $stock ) ) : ?>
								<div class="stm_featured_product_stock">
									<i class="fa-icon-stm_icon_user"></i><span><?php echo esc_attr( floatval( $stock ) ); ?></span>
								</div>
							<?php else : ?>
								<div class="stm_featured_product_stock">
									<i class="fa-icon-stm_icon_user"></i><span>0</span>
								</div>
							<?php endif; ?>

						</div>
						<div class="pull-right">
							<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
						</div>
					</div>

					<div class="stm_featured_product_show_more">
						<a class="btn btn-default" href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr( $add_to_cart_text ); ?>">
							<?php echo esc_html( $add_to_cart_text ); ?>
						</a>
					</div>
				</div>

			</div> <!-- stm_archive_product_inner_unit_centered -->
		</div> <!-- stm_archive_product_inner_unit -->
	</li>
<?php endif; ?>
