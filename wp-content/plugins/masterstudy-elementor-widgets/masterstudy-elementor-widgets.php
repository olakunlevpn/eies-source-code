<?php
/**
 * Plugin Name: Masterstudy Elementor Widgets
 * Description: Masterstudy Elementor Widgets.
 * Plugin URI:  https://masterstudy.stylemixthemes.com/
 * Version:     1.2.6
 * Author:      Stylemix
 * Author URI:  https://stylemixthemes.com/
 * Text Domain: masterstudy-elementor-widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'STM_CEW_PATH', dirname( __FILE__ ) );
define( 'STM_CEW_URL', plugin_dir_url( __FILE__ ) );
define( 'STM_LMS_ELEMENTOR_WIDGETS_VERSION', '1.2.6' );
define( 'STM_PATCH_VAR', 'patched8' );

require_once STM_CEW_PATH . '/patch/main.php';
require_once STM_CEW_PATH . '/patch-api/api.php';
require_once STM_CEW_PATH . '/helpers/custom_css.php';
require_once STM_CEW_PATH . '/import/import.php';

require_once STM_CEW_PATH . '/wp-custom-fields-theme-options/WPCFTO.php';
require_once STM_CEW_PATH . '/settings/hfe.php';

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_add_inline_style( 'elementor-frontend', cew_inline_css() );
	}
);

final class Masterstudy_Elementor_Widgets {


	public static $masterstudy_layout = '';

	const VERSION = '1.0.0';

	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	const MINIMUM_PHP_VERSION = '7.0';

	private static $_instance = null;

	public static function widgets_masterstudy() {
		$widgets = array(
			'certificate'            => 'Elementor_STM_Certificate',
			'contact'                => 'Elementor_STM_Contact',
			'contact_form_7'         => 'Elementor_STM_Contact_Form_7',
			'course_lessons'         => 'Elementor_STM_Course_Lessons',
			'color_separator'        => 'Elementor_STM_Colored_Separator',
			'countdown'              => 'Elementor_STM_Countddown',
			'buddypress_groups'      => 'Elementor_STM_Buddypress_Groups',
			'buddypress_instructors' => 'Elementor_STM_Buddypress_Instructors',
			'event_info'             => 'Elementor_STM_Event_Info',
			'events_grid'            => 'Elementor_STM_Events_Grid',
			'experts'                => 'Elementor_STM_Experts',
			'featured_products'      => 'Elementor_STM_Featured_Products',
			'gallery_grid'           => 'Elementor_STM_Gallery_Grid',
			'image_box'              => 'Elementor_STM_Image_Box',
			'icon_box'               => 'Elementor_STM_Icon_Box',
			'icon_button'            => 'Elementor_STM_Icon_Button',
			'mailchimp'              => 'Elementor_STM_Mailchimp',
			'multy_separator'        => 'Elementor_STM_Multy_Separator',
			'post_author'            => 'Elementor_STM_Post_Author',
			'post_comments'          => 'Elementor_STM_Post_Comments',
			'post_info'              => 'Elementor_STM_Post_Info',
			'post_list'              => 'Elementor_STM_Post_List',
			'post_tags'              => 'Elementor_STM_Post_Tags',
			'pricing_plan'           => 'Elementor_STM_Pricing_Plan',
			'product_categories'     => 'Elementor_STM_Product_Categories',
			'share'                  => 'Elementor_STM_Share',
			'sidebar'                => 'Elementor_STM_Sidebar',
			'sign_up_now'            => 'Elementor_STM_SignUpNow',
			'stats_counter'          => 'Elementor_STM_Stats_Counter',
			'teacher_detail'         => 'Elementor_STM_Teacher_Detail',
			'teachers_grid'          => 'Elementor_STM_Teachers_Grid',
			'testimonials'           => 'Elementor_STM_Testimonials',
			'stm_video'              => 'Elementor_STM_Video',
			'vc_custom_heading'      => 'Elementor_VC_Custom_Heading',
			'vc_cta'                 => 'Elementor_VC_CTA',
			'vc_pie_chart'           => 'Elementor_STM_Pie_Chart',
			'vc_empty_space'         => 'Elementor_STM_Empty_Space',
			'charts'                 => 'Elementor_STM_Charts',
			'flying_students'        => 'Elementor_STM_Flying_Students',
		);

		/*Header Widgets*/
		$widgets['header/wpml_dropdown'] = 'Elementor_STM_WPML_Dropdown';
		$widgets['header/popup_links']   = 'Elementor_STM_LMS_Popup_Links';

		if ( defined( 'STM_LMS_FILE' ) ) {
			$widgets['header/login']          = 'Elementor_STM_LMS_Login';
			$widgets['header/courses_search'] = 'Elementor_STM_LMS_Courses_Search';
			$widgets['header/sign_up']        = 'Elementor_STM_LMS_Sign_Up';
			$widgets['header/wishlist']       = 'Elementor_STM_LMS_Wishlist';
		}

		return $widgets;
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	public function __construct() {
		add_action( 'init', array( $this, 'i18n' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_categories' ) );
		add_filter( 'masterstudy_main_container_class', array( $this, 'container_class' ) );

		add_action( 'elementor/preview/enqueue_styles', array( $this, 'preview_styles' ) );
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'preview_scripts' ) );

		$this->add_default_controls();

		self::$masterstudy_layout = get_option( 'masterstudy_layout', 'layout_1' );

	}

	public function add_elementor_categories( $elements_manager ) {
		$new_category = array(
			'title' => esc_html__( 'MasterStudy Theme', 'masterstudy-elementor-widgets' ),
		);

		$categories = array_merge(
			array( 'stm_lms_theme' => $new_category ),
			$elements_manager->get_categories()
		);

		$set_categories = function( $categories ) {
			$this->categories = $categories;
		};

		$set_categories->call( $elements_manager, $categories );
	}

	public function preview_styles() {
		wp_enqueue_style( 'cew_pie_chart', STM_CEW_URL . '/assets/css/pie_chart.css', array(), time() );

		stm_module_styles( 'color_separator', 'style_1', array( 'stm_theme_style' ) );
		stm_module_styles( 'courses_grid', 'style_1', array( 'stm_theme_style' ) );
		stm_module_styles( 'certificate', 'style_1', array( 'stm_theme_style' ) );
		stm_module_styles( 'contact', 'style_1', array( 'stm_theme_style' ) );
		stm_module_styles( 'iconbox', 'style_1', array( 'stm_theme_style' ) );
		stm_module_styles( 'image_box', 'style_1', array( 'stm_theme_style' ) );

		stm_module_styles( 'testimonials', 'style_1', array( 'stm_theme_style' ) );
		stm_module_styles( 'testimonials', 'style_2', array( 'stm_theme_style' ) );
		stm_module_styles( 'testimonials', 'style_3', array( 'stm_theme_style' ) );
		stm_module_styles( 'testimonials', 'style_4', array( 'stm_theme_style' ) );
		stm_module_styles( 'testimonials', 'style_5', array( 'stm_theme_style' ) );
		stm_module_styles( 'testimonials', 'style_6', array( 'stm_theme_style' ) );
		stm_module_styles( 'testimonials', 'style_7', array( 'stm_theme_style' ) );
		stm_module_styles( 'testimonials', 'style_8', array( 'stm_theme_style' ) );

		stm_module_styles( 'featured_products', 'style_1', array( 'stm_theme_style' ) );

		stm_module_styles( 'hfe' );

		wp_enqueue_script( 'vue.js' );

		stm_module_styles( 'row_animations', 'flying_students' );

	}

	public function preview_scripts() {
		wp_enqueue_script( 'cew_script_preview', STM_CEW_URL . '/assets/js/elementor-preview.js', array( 'elementor-frontend' ), time(), true );
	}

	public function container_class( $class ) {
		$obj = get_queried_object();

		if ( empty( $obj->ID ) ) {
			return $class;
		}
		$item_id = $obj->ID;

		if ( ! in_array( get_post_type( $item_id ), get_option( 'elementor_cpt_support', array() ) ) ) {
			return $class;
		}

		$elementor_status = get_post_meta( $item_id, '_elementor_edit_mode', true );
		$elementor_status = ( ! empty( $elementor_status ) && 'builder' === $elementor_status );
		return ( $elementor_status ) ? '' : $class;
	}

	public function i18n() {
		load_plugin_textdomain( 'masterstudy-elementor-widgets' );

	}

	public function init() {
		/* Check if Elementor installed and activated */
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		/* Check for required Elementor version */
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		/* Check for required PHP version */
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		/* Add Plugin actions */
		add_action( 'elementor/widgets/register', array( $this, 'init_widgets' ) );
		add_action( 'elementor/controls/controls_registered', array( $this, 'init_controls' ) );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'widget_scripts' ) );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'widget_styles' ) );
	}

	public function widget_scripts() {
		wp_register_script( 'countdown', get_template_directory_uri() . '/assets/js/jquery.countdown.js', array( 'jquery' ), STM_LMS_ELEMENTOR_WIDGETS_VERSION, true );
		wp_enqueue_style( 'masterstudy-elementor-icons', plugins_url( 'assets/icons/ms-elementor-icons.css', __FILE__ ), array(), '0.1.0' );
		wp_enqueue_style( 'editor-styles', STM_CEW_URL . '/assets/css/elementor-editor.css', array(), time() );
		wp_enqueue_script( 'countdown' );
	}

	public function widget_styles() {
		wp_register_style( 'elementor-accordion', plugins_url( '/assets/css/accordion.css', __FILE__ ), array(), time() );
		wp_register_style( 'elementor-counter', plugins_url( '/assets/css/counter.css', __FILE__ ), array(), time() );
		wp_register_style( 'elementor-navigation-menu', plugins_url( '/assets/css/navigation-menu.css', __FILE__ ), array(), time() );

		wp_enqueue_style( 'elementor-accordion' );
		wp_enqueue_style( 'elementor-counter' );
		wp_enqueue_style( 'elementor-navigation-menu' );
	}

	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$allowed_html = wp_kses_allowed_html( 'post' );
		$message      = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'masterstudy-elementor-widgets' ),
			'<strong>' . esc_html__( 'Masterstudy Elementor widgets', 'masterstudy-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'masterstudy-elementor-widgets' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses( $message, $allowed_html ) );

	}

	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'masterstudy-elementor-widgets' ),
			'<strong>' . esc_html__( 'Masterstudy Elementor widgets', 'masterstudy-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'masterstudy-elementor-widgets' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html( $message ) );

	}

	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'masterstudy-elementor-widgets' ),
			'<strong>' . esc_html__( 'Masterstudy Elementor widgets', 'masterstudy-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'masterstudy-elementor-widgets' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html( $message ) );

	}

	public function include_widgets() {
		foreach ( $this->widgets_masterstudy() as $filename => $elementor_class ) {
			require_once __DIR__ . '/widgets/' . $filename . '.php';
		}
	}

	public function init_widgets() {
		/* Include Widget files */
		self::include_widgets();

		/* Register widget
		\Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_STM_Contact()); */
		foreach ( $this->widgets_masterstudy() as $filename => $elementor_class ) {
			$instance = new $elementor_class();
			\Elementor\Plugin::instance()->widgets_manager->register( $instance );
		}

	}

	public function init_controls() {
		/* Include Control files
		require_once(__DIR__ . '/controls/query_builder.php'); */

		/* Register control
		Elementor\Plugin::$instance->controls_manager->register_control('masterstudy-query-builder-control', new \MasterstudyQueryBuilderControl()); */
	}

	public static function get_post_type( $args = array() ) {
		$query = new WP_Query( $args );
		$r     = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$r[ get_the_ID() ] = get_the_title();
			}
			wp_reset_postdata();
		}

		return $r;
	}

	public static function get_image_url( $image_id, $size ) {
		require_once ELEMENTOR_PATH . 'includes/libraries/bfi-thumb/bfi-thumb.php';

		$attachment_size = $size;

		/*Check if custom size*/
		$custom_size = explode( 'x', $size );

		if ( ! empty( $custom_size[0] ) && ! empty( $custom_size[1] ) ) {
			if ( is_numeric( $custom_size[0] ) && is_numeric( $custom_size[1] ) ) {
				$attachment_size = array(
					// Defaults sizes
					0           => $custom_size[0], // Width.
					1           => $custom_size[1], // Height.

					'bfi_thumb' => true,
					'crop'      => true,
				);
			}
		}

		$image_src = wp_get_attachment_image_src( $image_id, $attachment_size );

		if ( ! empty( $image_src[0] ) ) {
			$image_src = $image_src[0];
		}

		if ( empty( $image_src[0] ) && 'thumbnail' !== $attachment_size ) {
			$image_src = wp_get_attachment_image_src( $image_id );
		}

		return $image_src;

	}

	public static function get_cropped_image( $image_id, $size ) {
		$image_url = self::get_image_url( $image_id, $size );
		$image_url = ( is_array( $image_url ) ) ? $image_url[0] : $image_url;
		$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

		return "<img data-src='{$image_url}' src='{$image_url}' alt='{$image_alt}' class=\"lazyload\" />";
	}

	public static function add_text_field( $vm, $id, $title, $default = '', $adds = array() ) {
		$args = array(
			'label' => $title,
			'type'  => \Elementor\Controls_Manager::TEXT,
		);

		$args = wp_parse_args( $adds, $args );

		if ( ! empty( $default ) ) {
			$args['default'] = $default;
		}

		$vm->add_control( $id, $args );

	}

	public static function add_query_builder( $vm, $prefix, $title = '' ) {
		$title = ( empty( $title ) ) ? esc_html__( 'Query Builder', 'masterstudy-elementor-widgets' ) : $title;

		$post_types = get_post_types( array( 'public' => true ) );
		$taxonomies = get_taxonomies();

		$vm->start_controls_section(
			"{$prefix}_query_builder_section",
			array(
				'label' => $title,
			)
		);

		$vm->add_control(
			"{$prefix}_query_builder_post_type",
			array(
				'label'    => __( 'Select post type', 'masterstudy-elementor-widgets' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => $post_types,
			)
		);

		$vm->add_control(
			"{$prefix}_query_builder_posts_per_page",
			array(
				'label' => __( 'Post count', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$vm->add_control(
			"{$prefix}_query_builder_order_by",
			array(
				'label'   => __( 'Order by', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => __( 'Date', 'masterstudy-elementor-widgets' ),
					'ID'            => __( 'ID', 'masterstudy-elementor-widgets' ),
					'author'        => __( 'Author', 'masterstudy-elementor-widgets' ),
					'title'         => __( 'Title', 'masterstudy-elementor-widgets' ),
					'modified'      => __( 'Modified', 'masterstudy-elementor-widgets' ),
					'rand'          => __( 'Rand', 'masterstudy-elementor-widgets' ),
					'comment_count' => __( 'Comment count', 'masterstudy-elementor-widgets' ),
					'menu_order'    => __( 'menu_order', 'masterstudy-elementor-widgets' ),
				),
			)
		);

		$vm->add_control(
			"{$prefix}_query_builder_sort_order",
			array(
				'label'   => __( 'Sort order', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'ASC'  => __( 'Ascending', 'masterstudy-elementor-widgets' ),
					'DESC' => __( 'Descending', 'masterstudy-elementor-widgets' ),
				),
			)
		);

		$vm->add_control(
			"{$prefix}_query_builder_post_ids",
			array(
				'label'       => __( 'Post IDs', 'masterstudy-elementor-widgets' ),
				'description' => __( 'Enter post IDs separated by comma. Ex.: 45,46,47', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
			)
		);

		$vm->add_control(
			"{$prefix}_query_builder_taxonomy",
			array(
				'label'    => __( 'Select taxonomy', 'masterstudy-elementor-widgets' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => $taxonomies,
			)
		);

		$vm->add_control(
			"{$prefix}_query_builder_categories",
			array(
				'label'       => __( 'Categories IDs', 'masterstudy-elementor-widgets' ),
				'description' => __( 'Enter category IDs separated by comma. Ex.: 45,46,47', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
			)
		);

		$vm->end_controls_section();
	}

	public static function get_query_builder( $settings, $prefix ) {
		/**
		 * "{$prefix}_query_builder_post_type"
		 * "{$prefix}_query_builder_posts_per_page"
		 * "{$prefix}_query_builder_order_by"
		 * "{$prefix}_query_builder_sort_order"
		 * "{$prefix}_query_builder_post_ids"
		 * "{$prefix}_query_builder_taxonomy"
		 * "{$prefix}_query_builder_categories"
		 */

		$post_type      = $settings[ "{$prefix}_query_builder_post_type" ];
		$posts_per_page = $settings[ "{$prefix}_query_builder_posts_per_page" ];
		$order_by       = $settings[ "{$prefix}_query_builder_order_by" ];
		$sort_order     = $settings[ "{$prefix}_query_builder_sort_order" ];
		$post_ids       = $settings[ "{$prefix}_query_builder_post_ids" ];
		$taxonomy       = $settings[ "{$prefix}_query_builder_taxonomy" ];
		$categories     = $settings[ "{$prefix}_query_builder_categories" ];

		$args = array();

		if ( ! empty( $post_type ) ) {
			$args['post_type'] = $post_type;
		}
		if ( ! empty( $posts_per_page ) ) {
			$args['posts_per_page'] = $posts_per_page;
		}
		if ( ! empty( $order_by ) ) {
			$args['order_by'] = $order_by;
		}
		if ( ! empty( $sort_order ) ) {
			$args['order'] = $sort_order;
		}
		if ( ! empty( $post_ids ) ) {
			$args['post__in'] = explode( ',', trim( $post_ids ) );
		}
		if ( ! empty( $taxonomy ) && ! empty( $categories ) ) {
			$args['tax_query'] = array(
				'relation' => 'AND',

			);

			foreach ( $taxonomy as $tax ) {
				$args['tax_query'][] = array(
					'taxonomy' => $tax,
					'field'    => 'id',
					'terms'    => explode( ',', trim( $categories ) ),
				);
			}
		}

		$q = new WP_Query( $args );

		return $q;

	}

	public static function add_font_settings( $vm, $prefix, $defaults = array(), $selector = '' ) {
		$tag_default = ( ! empty( $defaults['tag'] ) ) ? $defaults['tag'] : 'h2';

		$vm->add_control(
			"{$prefix}_tag",
			array(
				'label'   => __( 'Element tag', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => $tag_default,
				'options' => array(
					'h1'  => 'h1',
					'h2'  => 'h2',
					'h3'  => 'h3',
					'h4'  => 'h4',
					'h5'  => 'h5',
					'h6'  => 'h6',
					'p'   => 'p',
					'div' => 'div',
				),
			)
		);

		$vm->add_control(
			"{$prefix}_text_align",
			array(
				'label'   => __( 'Text align', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'    => __( 'Left', 'masterstudy-elementor-widgets' ),
					'right'   => __( 'Right', 'masterstudy-elementor-widgets' ),
					'center'  => __( 'Center', 'masterstudy-elementor-widgets' ),
					'justify' => __( 'Justify', 'masterstudy-elementor-widgets' ),
				),
			)
		);

		if ( ! empty( $selector ) ) {
			$vm->add_control(
				"{$prefix}_font_size",
				array(
					'label'     => __( 'Font size (px)', 'masterstudy-elementor-widgets' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'selectors' => array(
						"{{WRAPPER}} {$selector}" => 'font-size: {{VALUE}}px;',
					),
				)
			);

			$vm->add_control(
				"{$prefix}_line_height",
				array(
					'label'     => __( 'Line height (px)', 'masterstudy-elementor-widgets' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'selectors' => array(
						"{{WRAPPER}} {$selector}" => 'line-height: {{VALUE}}px;',
					),
				)
			);

			$vm->add_control(
				"{$prefix}_color",
				array(
					'label'     => __( 'Color', 'masterstudy-elementor-widgets' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						"{{WRAPPER}} {$selector}" => 'color: {{VALUE}};',
					),
				)
			);
		} else {
			$vm->add_control(
				"{$prefix}_font_size",
				array(
					'label' => __( 'Font size (px)', 'masterstudy-elementor-widgets' ),
					'type'  => \Elementor\Controls_Manager::TEXT,
				)
			);

			$vm->add_control(
				"{$prefix}_line_height",
				array(
					'label' => __( 'Line height (px)', 'masterstudy-elementor-widgets' ),
					'type'  => \Elementor\Controls_Manager::TEXT,
				)
			);
			$vm->add_control(
				"{$prefix}_color",
				array(
					'label' => __( 'Color', 'masterstudy-elementor-widgets' ),
					'type'  => \Elementor\Controls_Manager::COLOR,
				)
			);
		}

	}

	public static function get_font_settings( $settings, $prefix ) {
		$options = array(
			'tag',
			'text_align',
			'font_size',
			'line_height',
			'color',
		);

		$r = array();

		foreach ( $options as $option ) {
			$option_name  = "{$prefix}_{$option}";
			$r[ $option ] = ( ! empty( $settings[ $option_name ] ) ) ? $settings[ $option_name ] : '';
		}

		return $r;

	}

	public static function build_font_styles( $styles ) {
		$r = array();
		if ( ! empty( $styles['font_size'] ) ) {
			$r[] = "font-size : {$styles['font_size']}px;";
		}
		if ( ! empty( $styles['line_height'] ) ) {
			$r[] = "line-height : {$styles['line_height']}px;";
		}
		if ( ! empty( $styles['text_align'] ) ) {
			$r[] = "text-align : {$styles['text_align']};";
		}
		if ( ! empty( $styles['color'] ) ) {
			$r[] = "color : {$styles['color']};";
		}

		return $r;
	}

	public static function build_link( $settings, $param_name = 'link' ) {
		$url = array(
			'url' => $settings[ $param_name ]['url'],
		);

		$url['target'] = ( 'on' === $settings[ $param_name ]['is_external'] ) ? '_blank' : '';
		$url['title']  = '';
		$url['rel']    = ( 'on' === $settings[ $param_name ]['nofollow'] ) ? 'nofollow' : '';

		if ( ! empty( $settings[ "{$param_name}_label" ] ) ) {
			$url['title'] = $settings[ "{$param_name}_label" ];
		}

		return $url;
	}

	public static function parse_settings( $settings, $prefix ) {
		$r = array();

		foreach ( $settings as $key => $setting ) {

			$key_prefix = substr( $key, 0, strlen( $prefix ) );

			if ( $key_prefix !== $prefix ) {
				continue;
			}

			$r[ substr( $key, strlen( $prefix ) ) ] = $setting;

		}

		return $r;
	}

	public function add_default_controls() {
		add_action(
			'elementor/element/progress/section_progress/before_section_end',
			function ( $element, $args ) {
				// add a control
				$element->add_control(
					'customcolor', // update the control
					array(
						'label'     => __( 'Fill Background color', 'elementor-stm-widgets' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .elementor-progress-bar' => 'background-color: {{VALUE}}',
						),
					)
				);

				$element->add_control(
					'customtxtcolor', /* update the control */
					array(
						'label'     => __( 'Fill Background color', 'elementor-stm-widgets' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .elementor-progress-text' => 'color: {{VALUE}}',
						),
					)
				);

			},
			10,
			2
		);

		add_action(
			'elementor/element/image/section_image/before_section_end',
			function ( $element, $args ) {

				$element->add_control(
					'source',
					array(
						'label'        => __( 'Show Post thumbnail', 'masterstudy-elementor-widgets' ),
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => 'featured_image',
						'default'      => '',
					)
				);

			},
			10,
			2
		);

		add_action(
			'elementor/element/button/section_style/before_section_end',
			function ( $element, $args ) {

				/* add a control */

				$element->add_control(
					'more_options',
					array(
						'label'     => __( 'Button extra colors', 'plugin-name' ),
						'type'      => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					)
				);

				$element->start_controls_tabs( 'tabs_button_border_style' );

				$element->start_controls_tab(
					'tab_button_border_normal',
					array(
						'label' => __( 'Normal', 'elementor' ),
					)
				);

				$element->add_control(
					'vc_border_color', /* update the control */
					array(
						'label'     => __( 'Border color', 'elementor-stm-widgets' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} a.elementor-button' => 'border-color: {{VALUE}}',
						),
					)
				);

				$element->add_control(
					'vc_icon_color', /* update the control */
					array(
						'label'     => __( 'Icon color', 'elementor-stm-widgets' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} a.elementor-button .elementor-button-icon i' => 'color: {{VALUE}} !important',
						),
					)
				);

				$element->end_controls_tab();

				$element->start_controls_tab(
					'tab_button_border_hover',
					array(
						'label' => __( 'Hover', 'elementor' ),
					)
				);

				$element->add_control(
					'vc_border_color_hover', /* update the control */
					array(
						'label'     => __( 'Border color', 'elementor-stm-widgets' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} a.elementor-button:hover' => 'border-color: {{VALUE}}',
						),
					)
				);

				$element->add_control(
					'vc_icon_color_hover', /* update the control */
					array(
						'label'     => __( 'Icon color', 'elementor-stm-widgets' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} a.elementor-button:hover .elementor-button-icon i' => 'color: {{VALUE}} !important',
						),
					)
				);

				$element->end_controls_tab();

				$element->end_controls_tabs();

			},
			10,
			2
		);

		add_action(
			'elementor/element/button/section_button/before_section_end',
			function ( $element, $args ) {
				/* add a control */
				$element->add_control(
					'color_link',
					array(
						'label'        => __( 'Color Link', 'plugin-name' ),
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'separator'    => 'before',
						'return_value' => 'yes',
					)
				);

				$element->add_control(
					'button_block',
					array(
						'label'        => __( 'Set full width button?', 'plugin-name' ),
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'separator'    => 'before',
						'return_value' => 'yes',
					)
				);

			},
			10,
			2
		);

		add_action(
			'elementor/element/navigation-menu/section_layout/before_section_end',
			function ( $element, $args ) {
				/* add a control */
				$element->add_control(
					'menu_style',
					array(
						'label'        => esc_html__( 'Appearance', 'masterstudy-elementor-widgets' ),
						'type'         => \Elementor\Controls_Manager::SELECT,
						'default'      => '',
						'options'      => array(
							''        => esc_html__( 'Default', 'masterstudy-elementor-widgets' ),
							'style_1' => esc_html__( 'Style 1', 'masterstudy-elementor-widgets' ),
							'style_2' => esc_html__( 'Style 2', 'masterstudy-elementor-widgets' ),
							'style_3' => esc_html__( 'Style 3', 'masterstudy-elementor-widgets' ),
							'style_4' => esc_html__( 'Style 4', 'masterstudy-elementor-widgets' ),

						),
						'condition'    => array(
							'layout' => array( 'vertical' ),
						),
						'prefix_class' => 'elementor-navigation-menu-',
					)
				);

				$element->add_control(
					'menu_style_color',
					array(
						'label'     => __( 'Appearance Color', 'masterstudy-elementor-widgets' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-navigation-menu-style_1 .hfe-layout-vertical .hfe-nav-menu__layout-vertical .hfe-nav-menu li:before' => 'border-left-color: {{VALUE}}',
						),
						'condition' => array(
							'layout' => array( 'vertical' ),
						),
					)
				);

			},
			10,
			2
		);

		add_action(
			'elementor/element/accordion/section_title_style/before_section_end',
			function ( $element, $args ) {
				/* add a control */
				$element->add_control(
					'accordion_style',
					array(
						'label'        => esc_html__( 'Appearance', 'masterstudy-elementor-widgets' ),
						'type'         => \Elementor\Controls_Manager::SELECT,
						'default'      => '',
						'options'      => array(
							''        => esc_html__( 'Default', 'masterstudy-elementor-widgets' ),
							'style_1' => esc_html__( 'Style 1', 'masterstudy-elementor-widgets' ),

						),
						'prefix_class' => 'elementor-accordion-style-',
					)
				);

			},
			10,
			2
		);

		add_filter(
			'widget_text',
			function ( $content ) {
				return wpautop( $content );
			}
		);

		if ( get_option( 'masterstudy_layout' ) == 'layout_20' ) {
			add_action(
				'elementor/element/video/section_image_overlay/before_section_end',
				function ( $element ) {
					$element->add_control(
						'play_icon_text',
						array(
							'label'     => __( 'Play Icon Title', 'elementor' ),
							'type'      => \Elementor\Controls_Manager::TEXT,
							'condition' => array(
								'show_play_icon' => 'yes',
							),
							'separator' => 'before',
						)
					);
				},
				10,
				2
			);
		}

		add_filter(
			'masterstudy_secondary_font_classes',
			function ( $classes ) {
				$classes[] = '.elementor-progress-text, .elementor-tab-title a, .masterstudy_heading_font';
				$classes[] = '.elementor-widget-wp-widget-nav_menu ul li, .elementor-button-text, .elementor-tab-title';

				return $classes;
			}
		);

		add_action(
			'elementor/widget/render_content',
			function ( $content, $widget ) {

				$settings = $widget->get_settings();

				if ( 'wp-widget-search' === $widget->get_name() ) {
					$content = "<aside class='widget widget_search'>{$content}</aside>";
				}

				if ( 'wp-widget-categories' === $widget->get_name() ) {
					$content = "<aside class='widget widget_categories'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-archives' === $widget->get_name() ) {
					$content = "<aside class='widget widget_archive'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-tag_cloud' === $widget->get_name() ) {
					$content = "<aside class='widget widget_tag_cloud'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-pages' === $widget->get_name() ) {
					$content = "<aside class='widget widget_pages'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-nav_menu' === $widget->get_name() ) {
					$content = "<aside class='widget widget_nav_menu'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-text' === $widget->get_name() ) {
					$content = "<aside class='widget widget_text'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-recent-posts' === $widget->get_name() ) {
					$content = "<aside class='widget widget_recent_entries'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-meta' === $widget->get_name() ) {
					$content = "<aside class='widget widget_meta'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-recent-comments' === $widget->get_name() ) {
					$content = "<aside class='widget widget_recent_comments'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'wp-widget-calendar' === $widget->get_name() ) {
					$content = "<aside class='widget widget_calendar'>{$content}</aside>";
					$content = str_replace( '<h5>', '<h5 class="widget_title">', $content );
				}

				if ( 'button' === $widget->get_name() ) {
					$settings   = $widget->get_settings();
					$icon_align = ( ! empty( $settings['icon_align'] ) ) ? $settings['icon_align'] : '';
					if ( empty( $settings['selected_icon']['value'] ) ) {
						$icon_align = '';
					}
					$color_link         = $settings['color_link'];
					$color_link_class   = 'yes' === $color_link ? 'color_link' : '';
					$button_block_class = 'yes' === $settings['button_block'] ? 'button_block' : '';

					$content = str_replace( 'elementor-button-wrapper', "elementor-button-wrapper icon_align_{$icon_align} {$color_link_class} {$button_block_class}", $content );
				}

				if ( 'image' === $widget->get_name() ) {
					$settings = $widget->get_settings();

					$post_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

					if ( ! empty( $settings['source'] ) && ! empty( $post_image_url ) ) {
						$content = str_replace( $settings['image']['url'], $post_image_url[0], $content );
					}
				}

				if ( 'video' === $widget->get_name() ) {
					$settings = $widget->get_settings();
					if ( ! empty( $settings['play_icon_text'] ) && 'yes' === $settings['show_play_icon'] ) {
						$find         = array( 'elementor-custom-embed-play', '<span class="elementor-screen-only">Play Video</span>' );
						$replace_with = array( 'elementor-custom-embed-play has-play-icon-text', "<span>{$settings['play_icon_text']}</span>" );
						$content      = str_replace( $find, $replace_with, $content );
					}
				}
				return $content;

			},
			10,
			2
		);
	}

}

Masterstudy_Elementor_Widgets::instance();
