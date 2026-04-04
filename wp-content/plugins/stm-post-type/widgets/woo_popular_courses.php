<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Top Rated Products Widget.
 *
 * Gets and displays top rated products in an unordered list.
 *
 * @author   WooThemes
 * @category Widgets
 * @package  WooCommerce/Widgets
 * @version  3.0.0
 * @extends  WC_Widget
 */
class WC_Widget_Stm_Top_Rated_Products extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'stm_widget_top_rated_products';
		$this->widget_description = __( 'Display a list of your top rated products on your site with course expert.', 'stm-post-type' );
		$this->widget_id          = 'stm_widget_top_rated_products';
		$this->widget_name        = __( 'STM WooCommerce Top Rated Products', 'stm-post-type' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Popular courses', 'stm-post-type' ),
				'label' => __( 'Title', 'stm-post-type' ),
			),
			'style'  => array(
				'type'  => 'select',
				'std'   => __( 'style_1', 'stm-post-type' ),
				'options' => array(
					'style_1' => 'Style 1',
					'style_2' => 'Style 2',
				),
				'label' => __( 'Style', 'stm-post-type' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of products to show', 'stm-post-type' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];


		$style = ! empty( $instance['style'] ) ? $instance['style'] : $this->settings['style']['std'];

		$query_args = array(
			'posts_per_page' => $number,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_key'       => '_wc_average_rating',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'meta_query'     => WC()->query->get_meta_query(),
			'tax_query'      => WC()->query->get_tax_query(),
		);

		$r = new WP_Query( $query_args );

		if ( $r->have_posts() ) {

			$this->widget_start( $args, $instance );

			echo '<ul class="stm_product_list_widget widget_woo_stm_'.$style.'">';

			while ( $r->have_posts() ) {
				$r->the_post();
				global $product; ?>

				<?php $experts = get_post_meta($product->get_id(), 'course_expert', true);?>


				<?php if($style == 'style_1'): ?>
					<?php $stm_prod_image = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id()), 'img-50-50' ); ?>
					<li>
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr( $product->get_name() ); ?>">
							<img class="img-responsive" src="<?php echo esc_attr($stm_prod_image[0]); ?> " />
							<div class="meta">
								<div class="title"><?php echo stm_post_type_filtered_output($product->get_name()); ?></div>
								<?php if( !empty($experts) and $experts != 'no_expert' and (is_array($experts) && !in_array("no_expert", $experts) ) ): ?>
									<?php if( is_array($experts) ){ ?>
										<div class="expert"><?php _e('By', 'stm-post-type'); ?>
											<?php foreach( $experts as $expert ){ ?>
												<?php echo esc_attr(get_the_title($expert)); ?><br/>
											<?php }?>
										</div>
									<?php }else{ ?>
										<div class="expert"><?php _e('By', 'stm-post-type'); ?> <?php echo esc_attr(get_the_title($experts)); ?></div>
									<?php }?>
								<?php endif; ?>
							</div>
						</a>
					</li>
				<?php else: ?>
					<?php
					$stm_prod_image = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id()), 'img-75-75' );
					$regular_price = get_post_meta(get_the_id(), '_regular_price', true );
					$sale_price = get_post_meta(get_the_id(), '_sale_price', true );
					?>
					<li>
						<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_name() ); ?>">
							<img class="img-responsive" src="<?php echo esc_attr($stm_prod_image[0]); ?> " />
							<div class="meta">
								<div class="title h5"><?php echo stm_post_type_filtered_output($product->get_name()); ?></div>
								<div class="stm_featured_product_price">
									<?php if( ( empty($sale_price) and $sale_price != 0 ) or ( !empty($regular_price) and $regular_price != 0 ) ): ?>
										<?php if ( $price_html = $product->get_price_html() ) : ?>
											<span class="price"><?php echo stm_post_type_filtered_output($price_html); ?></span>
										<?php endif; ?>
									<?php else: ?>
										<div class="price price_free">
											<?php _e('Free', 'stm-post-type'); ?>
										</div>
									<?php endif; ?>
								</div>
								<div class="rating">
									<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
								</div>
								<?php if( !empty($experts) and $experts != 'no_expert' and (is_array($experts) && !in_array("no_expert", $experts) ) ): ?>
									<?php if( is_array($experts) ){ ?>
										<div class="expert">
											<?php foreach( $experts as $expert ){ ?>
												<?php echo esc_attr(get_the_title($expert)); ?><br/>
											<?php }?>
										</div>
									<?php }else{ ?>
										<div class="expert"><?php echo esc_attr(get_the_title($experts)); ?></div>
									<?php }?>
								<?php endif; ?>
							</div>
						</a>
					</li>
				<?php endif; ?>
			<?php }

			echo '</ul>';

			$this->widget_end( $args );
		}

		wp_reset_postdata();

		$content = ob_get_clean();

		echo stm_post_type_filtered_output($content);

		$this->cache_widget( $args, $content );
	}
}

function wpb_load_widget() {
	register_widget( 'WC_Widget_Stm_Top_Rated_Products' );
}

add_action( 'widgets_init', 'wpb_load_widget' );