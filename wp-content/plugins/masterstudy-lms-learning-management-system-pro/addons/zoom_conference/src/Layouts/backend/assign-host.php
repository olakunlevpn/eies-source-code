<?php
// phpcs:ignoreFile
if ( ! empty( $_POST['host_id'] ) ) {
	$hosts = (array) $_POST['host_id'];
	foreach ( $hosts as $user_id => $host_id ) {
		if ( isset( $host_id ) ) {
			$host_id = sanitize_text_field( $host_id );
			update_user_meta( $user_id, 'MSLMS_ZOOM_host_id', $host_id );
		}
	}
}

$users = get_users(); ?>
<div class="assign_host_page">
	<h1><?php esc_html_e( 'Assign Zoom host id to WordPress users', 'masterstudy-lms-learning-management-system-pro' ); ?></h1>
	<h3><?php esc_html_e( 'For developers only', 'masterstudy-lms-learning-management-system-pro' ); ?></h3>
	<p>
		<?php
		_e( 'Copy Host ID from users <a href="' . esc_url( admin_url( '/admin.php?page=mslms_zoom_users' ) ) . '">page</a> and past to Host ID input field. You can get this data by <code>get_user_meta( $user_id, "mslms_zoom_host_id", true );</code>', 'masterstudy-lms-learning-management-system-pro' );
		?>
	</p>
	<div class="MSLMS_ZOOM_table-wrap stm-lms-tab active stm_metaboxes_grid" style="max-width: calc(100% - 20px);">
		<form method="post" class="stm_metaboxes_grid__inner">
			<table class="MSLMS_ZOOM_table">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Email', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
					<th><?php esc_html_e( 'Username', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
					<th><?php esc_html_e( 'Host ID', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $users ) ): ?>
					<?php foreach ( $users as $user ): ?>
						<?php
						$user_id       = $user->ID;
						$user_nicename = $user->user_nicename;
						$user_email    = $user->user_email;
						$host_id       = get_user_meta( $user_id, 'MSLMS_ZOOM_host_id', true );
						?>
						<tr>
							<td><?php echo esc_html( $user_email ); ?></td>
							<td><?php echo esc_html( $user_nicename ); ?></td>
							<td><input type="text" value="<?php echo esc_attr( $host_id ); ?>" name="host_id[<?php echo esc_attr( $user_id ); ?>]" data-user_id="<?php echo esc_attr( $user_id ); ?>" class="assign_host_id"/></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
			<button class="button load_button" type="submit"><?php esc_html_e( 'Update', 'masterstudy-lms-learning-management-system-pro' ) ?></button>
		</form>
	</div>
</div>
