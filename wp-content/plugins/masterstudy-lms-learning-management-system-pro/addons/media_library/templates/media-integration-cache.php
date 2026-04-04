<?php

use MasterStudy\Lms\Pro\addons\media_library\Services\Client;

$cache_exists = Client::is_cache_exists();
$cache_size   = array(
	'total_size_mb' => 0,
);

if ( $cache_exists ) {
	$cache_size = Client::get_cache_size();
}

wp_enqueue_script( 'masterstudy-lms-media-library-settings', STM_LMS_PRO_URL . 'assets/js/media-library/settings.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
wp_enqueue_style( 'masterstudy-lms-media-library-settings', STM_LMS_PRO_URL . 'assets/css/media-library/settings.css', array(), STM_LMS_PRO_VERSION );
?>

<div class="wpcfto_generic_field wpcfto_generic_field__select">
	<div class="integration-usage-section">
		<div class="section-title">
			<span><?php echo esc_html__( 'Integrations cache', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
		</div>
		<div class="usage-section">
			<?php
			if ( $cache_exists ) :
				?>
				<span class="integration-cache-exists-text"><?php echo esc_html__( 'Existing Cache Detected', 'masterstudy-lms-learning-management-system-pro' ); ?>: <?php echo esc_attr( $cache_size['total_size_mb'] ); ?> MB</span>
				<button class="button integration-clear-cache-button"><?php echo esc_html__( 'Clear cache', 'masterstudy-lms-learning-management-system-pro' ); ?></button>
			<?php else : ?>
			<span class="integration-cache-exists-text"><?php echo esc_html__( 'No cache', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
			<?php endif; ?>
		</div>

	</div>
</div>
