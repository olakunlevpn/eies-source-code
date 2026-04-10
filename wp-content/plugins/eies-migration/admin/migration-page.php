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
		</tbody>
	</table>

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
