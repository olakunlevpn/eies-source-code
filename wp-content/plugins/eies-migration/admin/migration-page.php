<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$nonce = wp_create_nonce( 'eies_migration_nonce' );

// Get current counts
global $wpdb;
$map_table = $wpdb->prefix . 'eies_migration_map';
$map_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$map_table}'" );

$counts = array(
	'categories'  => 0,
	'users'       => 0,
	'courses'     => 0,
	'quizzes'     => 0,
	'enrollments' => 0,
);

if ( $map_exists ) {
	$counts['categories']  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$map_table} WHERE entity_type = 'category'" );
	$counts['users']       = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$map_table} WHERE entity_type = 'user'" );
	$counts['courses']     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$map_table} WHERE entity_type = 'course'" );
	$counts['quizzes']     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$map_table} WHERE entity_type = 'quiz'" );
	$counts['enrollments'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}stm_lms_user_courses" );
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'EIES Moodle → MasterStudy Migration', 'eies-migration' ); ?></h1>

	<p><?php esc_html_e( 'Migrate data from Moodle LMS to MasterStudy LMS step by step.', 'eies-migration' ); ?></p>

	<table class="widefat striped" style="max-width: 700px;">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Step', 'eies-migration' ); ?></th>
				<th><?php esc_html_e( 'Status', 'eies-migration' ); ?></th>
				<th><?php esc_html_e( 'Action', 'eies-migration' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><strong>1. <?php esc_html_e( 'Categories', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( '25 Moodle categories → MasterStudy taxonomy', 'eies-migration' ); ?></small></td>
				<td id="status-categories"><?php echo $counts['categories'] > 0 ? $counts['categories'] . ' migrated' : 'Pending'; ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="categories"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>2. <?php esc_html_e( 'Users', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( '6,558 users with roles', 'eies-migration' ); ?></small></td>
				<td id="status-users"><?php echo $counts['users'] > 0 ? $counts['users'] . ' migrated' : 'Pending'; ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="users"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>3. <?php esc_html_e( 'Courses & Lessons', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( '221 courses with curriculum structure', 'eies-migration' ); ?></small></td>
				<td id="status-courses"><?php echo $counts['courses'] > 0 ? $counts['courses'] . ' migrated' : 'Pending'; ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="courses"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>4. <?php esc_html_e( 'Quizzes & Questions', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( '276 quizzes, 9,317 questions', 'eies-migration' ); ?></small></td>
				<td id="status-quizzes"><?php echo $counts['quizzes'] > 0 ? $counts['quizzes'] . ' migrated' : 'Pending'; ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="quizzes"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>5. <?php esc_html_e( 'Enrollments & Grades', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( '4,761 enrollments with progress', 'eies-migration' ); ?></small></td>
				<td id="status-enrollments"><?php echo $counts['enrollments'] > 0 ? $counts['enrollments'] . ' migrated' : 'Pending'; ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="enrollments"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>6. <?php esc_html_e( 'Files & Images', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( 'Course images, lesson files, user avatars', 'eies-migration' ); ?></small></td>
				<td id="status-files"><?php
					$file_count = $map_exists ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$map_table} WHERE entity_type LIKE 'file_%'" ) : 0;
					echo $file_count > 0 ? $file_count . ' migrated' : 'Pending';
				?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="files"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>7. <?php esc_html_e( 'Old WordPress Data', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( 'Import images, descriptions, prices from old eies.com.bo WooCommerce products', 'eies-migration' ); ?></small></td>
				<td id="status-wp_data"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_data"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr style="background: #f0f6fc;">
				<td colspan="3"><strong><?php esc_html_e( 'Phase 2: WordPress Data Migration', 'eies-migration' ); ?></strong></td>
			</tr>
			<tr>
				<td><strong>8. <?php esc_html_e( 'Merge/Import WP Users', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( '9,234 WP users — merge 5,623 existing + import 3,609 new with billing data', 'eies-migration' ); ?></small></td>
				<td id="status-wp_users"><?php
					$wp_user_count = $map_exists ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$map_table} WHERE entity_type = 'wp_user'" ) : 0;
					echo $wp_user_count > 0 ? $wp_user_count . ' processed' : 'Pending';
				?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_users"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>9. <?php esc_html_e( 'Import WP Products as Courses', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( '87 WP-only products → MasterStudy courses with images, prices, categories', 'eies-migration' ); ?></small></td>
				<td id="status-wp_products"><?php
					$wp_prod_count = $map_exists ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$map_table} WHERE entity_type = 'wp_product'" ) : 0;
					echo $wp_prod_count > 0 ? $wp_prod_count . ' processed' : 'Pending';
				?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_products"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>10. <?php esc_html_e( 'Import WooCommerce Orders', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( '4,003 orders with items, notes, and user mapping', 'eies-migration' ); ?></small></td>
				<td id="status-wp_orders"><?php
					$wp_order_count = $map_exists ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$map_table} WHERE entity_type = 'wp_order'" ) : 0;
					echo $wp_order_count > 0 ? $wp_order_count . ' imported' : 'Pending';
				?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_orders"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>11. <?php esc_html_e( 'Reviews, Custom Tables, Testimonials, Coupons', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( 'Reviews, sys_evaluaciones, sys_lista_correos, 6 testimonials, 5 coupons', 'eies-migration' ); ?></small></td>
				<td id="status-wp_content"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_content"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr style="background: #fff8e5;">
				<td colspan="3"><strong><?php esc_html_e( '12. Site Settings (granular sub-steps)', 'eies-migration' ); ?></strong><br><small><?php esc_html_e( 'Run each sub-step independently. Pay attention to safety badges — items marked UNSAFE may overwrite theme-specific content built for the old MasterStudy parent theme and break the new starter theme rendering.', 'eies-migration' ); ?></small></td>
			</tr>
			<tr>
				<td><strong>12a. <?php esc_html_e( 'Required Plugins', 'eies-migration' ); ?></strong> <span class="eies-badge eies-safe">SAFE</span><br><small><?php esc_html_e( 'Installs livees-checkout, woocommerce-currency-switcher, pymntpl-paypal-woocommerce, ar-contactus.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_plugins"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_plugins"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12b. <?php esc_html_e( 'Site Identity', 'eies-migration' ); ?></strong> <span class="eies-badge eies-safe">SAFE</span><br><small><?php esc_html_e( 'blogname, tagline, admin email, language, timezone, date/time format, permalinks.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_identity"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_identity"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12c. <?php esc_html_e( 'WooCommerce Settings', 'eies-migration' ); ?></strong> <span class="eies-badge eies-warn">CAUTION</span><br><small><?php esc_html_e( 'Currency (BOB), price format, store address, plus binds Cart/Shop/Checkout/My-account WC pages. Best run AFTER pages exist (12f or theme defaults).', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_wc"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_wc"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12d. <?php esc_html_e( 'Payment Gateways', 'eies-migration' ); ?></strong> <span class="eies-badge eies-safe">SAFE</span><br><small><?php esc_html_e( 'BACS, LCKout, PayPal, PPCP gateway settings.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_payment"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_payment"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12e. <?php esc_html_e( 'Currency Switcher (WOOCS)', 'eies-migration' ); ?></strong> <span class="eies-badge eies-safe">SAFE</span><br><small><?php esc_html_e( 'All woocs_* options for multi-currency.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_currency"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_currency"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12f. <?php esc_html_e( '13 Pages (Inicio, Nosotros, Contacto, Tienda, Carrito, Mi cuenta, Galerías, etc.)', 'eies-migration' ); ?></strong> <span class="eies-badge eies-danger">UNSAFE</span><br><small style="color:#a00;"><?php esc_html_e( 'WARNING: Page content was built for the old full MasterStudy parent theme (Elementor + stm_mailchimp / stm_testimonials shortcodes that DO NOT exist in ms-lms-starter-theme). Importing will produce broken layouts and unprocessed shortcodes. Only run if you switch back to the full MasterStudy theme. Skip this if using ms-lms-starter-theme — use theme demo content instead.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_pages"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button eies-migrate-btn" style="background:#dc3545;color:#fff;border-color:#a00;" data-step="wp_settings_pages" data-confirm="Imports 13 pages built for OLD MasterStudy theme. Will break layout on ms-lms-starter-theme. Continue?"><?php esc_html_e( 'Run (Risky)', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12g. <?php esc_html_e( 'Media Library (635 files)', 'eies-migration' ); ?></strong> <span class="eies-badge eies-safe">SAFE</span><br><small><?php esc_html_e( 'Copies attachments + metadata. Theme-agnostic.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_media"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_media"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12h. <?php esc_html_e( 'Contact Form 7 (6 forms)', 'eies-migration' ); ?></strong> <span class="eies-badge eies-safe">SAFE</span><br><small><?php esc_html_e( 'CF7 form definitions + meta. Theme-agnostic.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_cf7"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_cf7"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12i. <?php esc_html_e( 'Main Menu', 'eies-migration' ); ?></strong> <span class="eies-badge eies-warn">CAUTION</span><br><small><?php esc_html_e( 'Creates "Main menu" with 6 items + assigns to "primary" location. Items reference imported pages (12f). On ms-lms-starter-theme the location key is ms-lms-starter-theme-main-menu — you may need to re-assign manually after import.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_menu"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_menu"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr>
				<td><strong>12j. <?php esc_html_e( 'AR Contactus', 'eies-migration' ); ?></strong> <span class="eies-badge eies-safe">SAFE</span><br><small><?php esc_html_e( 'Custom contact tables + plugin options.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings_ar"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button button-primary eies-migrate-btn" data-step="wp_settings_ar"><?php esc_html_e( 'Run', 'eies-migration' ); ?></button></td>
			</tr>
			<tr style="background: #f6f7f7;">
				<td><strong>12. <?php esc_html_e( 'Run ALL of step 12 in sequence (legacy)', 'eies-migration' ); ?></strong> <span class="eies-badge eies-warn">INCLUDES UNSAFE</span><br><small><?php esc_html_e( 'Runs every sub-step above including 12f Pages. Kept for backward compatibility — prefer running sub-steps individually.', 'eies-migration' ); ?></small></td>
				<td id="status-wp_settings"><?php esc_html_e( 'Pending', 'eies-migration' ); ?></td>
				<td><button class="button eies-migrate-btn" style="background:#996600;color:#fff;border-color:#663300;" data-step="wp_settings" data-confirm="Runs ALL sub-steps including 12f Pages which breaks the starter theme. Continue?"><?php esc_html_e( 'Run All', 'eies-migration' ); ?></button></td>
			</tr>
		</tbody>
	</table>

	<style>
		.eies-badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:11px; font-weight:600; vertical-align:middle; margin-left:6px; }
		.eies-safe { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
		.eies-warn { background:#fff3cd; color:#856404; border:1px solid #ffeaa7; }
		.eies-danger { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
	</style>

	<br>
	<button class="button button-secondary" id="eies-reset-btn" style="color: #a00;"><?php esc_html_e( 'Reset All Migrated Data', 'eies-migration' ); ?></button>

	<div id="eies-migration-log" style="margin-top: 20px; padding: 15px; background: #1d2327; color: #50c878; font-family: monospace; font-size: 13px; max-height: 300px; overflow-y: auto; display: none; border-radius: 4px;"></div>
</div>

<script>
jQuery(document).ready(function($) {
	var nonce = '<?php echo esc_js( $nonce ); ?>';
	var $log = $('#eies-migration-log');

	function log(msg) {
		$log.show();
		$log.append(msg + "\n");
		$log.scrollTop($log[0].scrollHeight);
	}

	$('.eies-migrate-btn').on('click', function() {
		var $btn = $(this);
		var step = $btn.data('step');
		var confirmMsg = $btn.data('confirm');
		if (confirmMsg && !confirm(confirmMsg)) return;
		$btn.prop('disabled', true).text('Running...');
		log('[' + new Date().toLocaleTimeString() + '] Starting ' + step + ' migration...');

		$.post(ajaxurl, {
			action: 'eies_run_migration',
			nonce: nonce,
			step: step
		}, function(response) {
			$btn.prop('disabled', false).text('Run');
			if (response.success) {
				$('#status-' + step).html('<span style="color:green;">' + response.message + '</span>');
				log('[' + new Date().toLocaleTimeString() + '] ✓ ' + response.message);
			} else {
				$('#status-' + step).html('<span style="color:red;">' + response.message + '</span>');
				log('[' + new Date().toLocaleTimeString() + '] ✗ ' + response.message);
			}
		}).fail(function(xhr) {
			$btn.prop('disabled', false).text('Run');
			log('[' + new Date().toLocaleTimeString() + '] ✗ Error: ' + xhr.statusText);
			$('#status-' + step).html('<span style="color:red;">Failed - check server logs</span>');
		});
	});

	$('#eies-reset-btn').on('click', function() {
		if (!confirm('This will DELETE all migrated data. Are you sure?')) return;
		var $btn = $(this);
		$btn.prop('disabled', true).text('Resetting...');
		log('[' + new Date().toLocaleTimeString() + '] Resetting all migrated data...');

		$.post(ajaxurl, {
			action: 'eies_run_migration',
			nonce: nonce,
			step: 'reset'
		}, function(response) {
			$btn.prop('disabled', false).text('Reset All Migrated Data');
			log('[' + new Date().toLocaleTimeString() + '] ' + response.message);
			$('[id^="status-"]').text('Pending');
		});
	});
});
</script>
