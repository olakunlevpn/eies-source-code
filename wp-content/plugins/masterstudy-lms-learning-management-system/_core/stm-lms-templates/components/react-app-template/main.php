<?php
/**
 * React app template for easy creation of new apps inside wp_admin
 *
 * @var string $app_id - react app id
 * @var array|null $react_vars - variables that will be passed to react app
 *
 * @package masterstudy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use MasterStudy\Lms\Plugin\Addons;

global $ms_lms_loaded_textdomain_path;
do_action( 'admin_head' );

$translations_path = ! empty( $ms_lms_loaded_textdomain_path ) ? $ms_lms_loaded_textdomain_path : MS_LMS_PATH . '/languages';

wp_enqueue_script( $app_id . '-vendors', apply_filters( $app_id . '_vendors_js', MS_LMS_URL . 'assets/course-builder/js/vendors.js' ), array(), MS_LMS_VERSION, true );
wp_enqueue_script( $app_id . '-translations', apply_filters( $app_id . 'translations_js', MS_LMS_URL . 'assets/course-builder/js/i18n-translations.js' ), array(), MS_LMS_VERSION, true );
wp_enqueue_script( $app_id, apply_filters( $app_id, MS_LMS_URL . 'assets/course-builder/js/main.js' ), array(), MS_LMS_VERSION, true );

if ( ! empty( $react_vars ) ) {
	wp_localize_script(
		$app_id,
		$react_vars['object_name'],
		$react_vars['vars'],
	);
}

wp_localize_script(
	$app_id,
	'react_default_vars',
	array(
		'admin_url'      => admin_url(),
		'currency_info'  => array(
			'currency_symbol'    => \STM_LMS_Options::get_option( 'currency_symbol', '$' ),
			'decimals_num'       => \STM_LMS_Options::get_option( 'decimals_num', '2' ),
			'currency_thousands' => \STM_LMS_Options::get_option( 'currency_thousands', ' ' ),
			'currency_decimals'  => \STM_LMS_Options::get_option( 'currency_decimals', '.' ),
			'currency_position'  => \STM_LMS_Options::get_option( 'currency_position', 'left' ),
		),
		'enabled_addons' => Addons::enabled_addons(),
	)
);

wp_set_script_translations( $app_id . '-translations', 'masterstudy-lms-learning-management-system', $translations_path );

$scripts      = wp_scripts();
$load_scripts = array(
	'wp-polyfill-inert',
	'regenerator-runtime',
	'wp-polyfill',
	'wp-hooks',
	'wp-i18n',
	'utils',
);
?>
<div id="<?php echo esc_attr( $app_id ); ?>" class="ms-react-app__no-container-padding"></div>
<script>
	window.lmsApiSettings = {
		lmsUrl: '<?php echo esc_url_raw( rest_url( 'masterstudy-lms/v2' ) ); ?>',
		wpUrl: '<?php echo esc_url_raw( rest_url( 'wp/v2' ) ); ?>',
		nonce: '<?php echo esc_html( wp_create_nonce( 'wp_rest' ) ); ?>',
		isWpAdmin: true
	};

	<?php if ( function_exists( 'pll_current_language' ) ) { ?>
	window.lmsApiSettings.lang = '<?php echo esc_js( pll_current_language() ); ?>';
	<?php } ?>

	window.lmsApiSettings.locale = '<?php echo esc_attr( get_locale() ); ?>';
	window.lmsApiSettings.wp_date_format = '<?php echo esc_attr( get_option( 'date_format' ) ); ?>';
	window.lmsApiSettings.wp_time_format = '<?php echo esc_attr( get_option( 'time_format' ) ); ?>';
</script>
<?php
foreach ( $load_scripts as $handle ) {
	$handle_src = $scripts->registered[ $handle ]->src;
	$src_url    = filter_var( $handle_src, FILTER_VALIDATE_URL ) ? $handle_src : site_url( $handle_src );
	?>
	<script src="<?php echo esc_url( $src_url ); // phpcs:ignore ?>"></script>
<?php } ?>
