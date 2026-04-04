<?php

use Elementor\Controls_Manager;

class Elementor_STM_Featured_Products extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_featured_products';
	}

	public function get_title() {
		return esc_html__( 'Products List (All, featured, teacher courses)', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-featured_products lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {  }

	protected function register_controls() {
		$experts = array(
			'no_expert' => 'Choose expert for course',
		);

		$experts_args = array(
			'post_type'      => 'teachers',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$experts_query = new WP_Query( $experts_args );

		if ( intval( $experts_query->found_posts ) > 0 ) {
			foreach ( $experts_query->posts as $expert ) {
				$experts[ $expert->ID ] = $expert->post_title;
			};
		}

		wp_reset_query();

		$stm_product_categories = array();

		$all_product_categories = get_terms( 'product_cat', array( 'hide_empty' => true ) );

		if ( $all_product_categories && ! is_wp_error( $all_product_categories ) ) {
			foreach ( $all_product_categories as $category ) {
				$stm_product_categories[ $category->slug ] = $category->name;
			}
		}

		$stm_product_tags = array();

		$all_product_tags = get_terms( 'product_tag', array( 'hide_empty' => true ) );

		if ( $all_product_tags && ! is_wp_error( $all_product_tags ) ) {
			foreach ( $all_product_tags as $tag ) {
				$stm_product_tags[ $tag->slug ] = $tag->name;
			}
		}

		$order_by_values = array(
			'date'          => __( 'Date', 'masterstudy' ),
			'ID'            => __( 'ID', 'masterstudy' ),
			'author'        => __( 'Author', 'masterstudy' ),
			'title'         => __( 'Title', 'masterstudy' ),
			'modified'      => __( 'Modified', 'masterstudy' ),
			'rand'          => __( 'Random', 'masterstudy' ),
			'comment_count' => __( 'Comment count', 'masterstudy' ),
			'menu_order'    => __( 'Menu order', 'masterstudy' ),
		);

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-stm-widgets' ),
			)
		);

		$this->add_control(
			'meta_key',
			array(
				'label'   => __( 'Meta sorting key', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'all'       => 'All',
					'_featured' => 'Featured',
					'expert'    => 'Expert',
					'category'  => 'Category',
					'tag'       => 'Tag',
				),
				'default' => 'all',
			)
		);

		$this->add_control(
			'expert_id',
			array(
				'label'   => __( 'Choose expert', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $experts,
				'default' => 'no_expert',
			)
		);

		$this->add_control(
			'product_tag_id',
			array(
				'label'   => __( 'Choose tag', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $stm_product_tags,
				'default' => 'no_tag',
			)
		);

		$this->add_control(
			'category_id',
			array(
				'label'   => __( 'Choose category', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $stm_product_categories,
				'default' => 'no_category',
			)
		);

		$this->add_control(
			'view_type',
			array(
				'label'   => __( 'View type', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'featured_products_list'     => 'List',
					'featured_products_carousel' => 'Carousel',
				),
				'default' => 'featured_products_carousel',
			)
		);

		$this->add_control(
			'auto',
			array(
				'label' => __( 'Carousel Auto Scroll', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'per_page',
			array(
				'label'       => __( 'Number of items to output', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => __( 'Type Number of items here', 'masterstudy-elementor-widgets' ),
				'description' => __( 'Leave empty to display all', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'per_row',
			array(
				'label'   => __( 'Number of items per row', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					4 => '4',
					3 => '3',
					2 => '2',
					1 => '1',
				),
				'default' => 4,
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'DESC' => 'Descending',
					'ASC'  => 'Ascending',
				),
				'default' => 'DESC',
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order by', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $order_by_values,
				'default' => 'date',
			)
		);

		$this->add_control(
			'hide_price',
			array(
				'label' => __( 'Don\'t Show price', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'hide_rating',
			array(
				'label' => __( 'Don\'t Show rating', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'hide_comments',
			array(
				'label' => __( 'Don\'t Show comments number', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'price_bg',
			array(
				'label' => __( 'Price Badge background color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'free_price_bg',
			array(
				'label' => __( 'Price Badge (Free) background color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_featured_products_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_featured_products_';

			$settings['featured_product_num'] = stm_create_unique_id( $settings );

			masterstudy_show_template( 'featured_products', $settings );

		}
	}

	protected function content_template() {
	}

}
