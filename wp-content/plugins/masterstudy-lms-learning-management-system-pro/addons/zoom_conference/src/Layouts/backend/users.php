<?php
// phpcs:ignoreFile

if ( ! empty( $_GET['delete_zoom_users_cache'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'zoom_users_nonce' ) ) {
	delete_transient( 'mslms_zoom_users' );
	$redirect_url = remove_query_arg(
		array( '_wpnonce', 'delete_zoom_users_cache' )
	);
	wp_safe_redirect( $redirect_url );
}

$page_number = 1;

if ( ! empty( $_GET['page_number'] ) ) {
	$page_number = intval( $_GET['page_number'] );
}

$users_info = MSLMS_StmZoom::MSLMS_ZOOM_get_users_pagination( $page_number );
$users      = isset( $users_info['users'] ) ? $users_info['users'] : array();
$page_count = isset( $users_info['page_count'] ) ? $users_info['page_count'] : 1;
$nonce      = wp_create_nonce( 'zoom_users_nonce' );
?>
<div class="report_wrap wpcfto-settings">
	<h1>
		<span><?php esc_html_e( 'Zoom Conference Users', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
		<a class="button load_button" href="<?php echo esc_url( admin_url( 'admin.php?page=mslms_zoom_add_user' ) ); ?>">
			<span><?php esc_html_e( 'Add User', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
		</a>
	</h1>
	<div>
		<a href="
		<?php
		echo esc_url(
			add_query_arg(
				array(
					'delete_zoom_users_cache' => '1',
					'_wpnonce'                => $nonce,
				)
			)
		);
		?>
		"><?php esc_html_e( 'Delete cache', 'masterstudy-lms-learning-management-system-pro' ); ?></a>
	</div>
	<div class="MSLMS_ZOOM_table-wrap">
		<table class="MSLMS_ZOOM_table">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Host ID', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
				<th><?php esc_html_e( 'Email', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
				<th><?php esc_html_e( 'Name', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
				<th><?php esc_html_e( 'Last name', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
				<th><?php esc_html_e( 'Created on', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
				<th><?php esc_html_e( 'Last login', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
				<th><?php esc_html_e( 'Last Client', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
				<th><?php esc_html_e( 'Status', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
			</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $users ) ) : ?>
					<?php foreach ( $users as $user ) : ?>
						<?php
						$id                  = ! empty( $user['id'] ) ? $user['id'] : '';
						$email               = ! empty( $user['email'] ) ? $user['email'] : '';
						$first_name          = ! empty( $user['first_name'] ) ? $user['first_name'] : '';
						$last_name           = ! empty( $user['last_name'] ) ? $user['last_name'] : '';
						$created_at          = ! empty( $user['created_at'] ) ? $user['created_at'] : '';
						$last_login_time     = ! empty( $user['last_login_time'] ) ? $user['last_login_time'] : '';
						$last_client_version = ! empty( $user['last_client_version'] ) ? $user['last_client_version'] : '';
						$status              = ! empty( $user['status'] ) ? $user['status'] : '';
						?>
					<tr>
						<td><?php echo esc_html( $id ); ?></td>
						<td><?php echo esc_html( $email ); ?></td>
						<td><?php echo esc_html( $first_name ); ?></td>
						<td><?php echo esc_html( $last_name ); ?></td>
						<td><?php echo esc_html( $created_at ); ?></td>
						<td><?php echo esc_html( $last_login_time ); ?></td>
						<td><?php echo esc_html( $last_client_version ); ?></td>
						<td><?php echo esc_html( $status ); ?></td>
					</tr>
				<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="8" align="center"><?php esc_html_e( 'Users not found!', 'masterstudy-lms-learning-management-system-pro' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php if ( $page_count > 1 ) : ?>
		<div class="mslms_zoom_users_pagination">
			<?php
			$args = array(
				'base'      => '%_%',
				'format'    => '?page_number=%#%',
				'total'     => $page_count,
				'current'   => $page_number,
				'prev_text' => '«',
				'next_text' => '»',
				'type'      => 'list',
			);
			echo wp_kses_post( paginate_links( $args ) );
			?>
		</div>
	<?php endif; ?>
</div>
