<?php
// phpcs:ignoreFile

if(!empty($_GET['page']) && $_GET['page'] === 'mslms_zoom_users') {
    require_once MSLMS_ZOOM_PATH . '/src/Layouts/backend/users.php';
}
else if(!empty($_GET['page']) && $_GET['page'] === 'mslms_zoom_add_user') {
    require_once MSLMS_ZOOM_PATH . '/src/Layouts/backend/add-users.php';
}
else if(!empty($_GET['page']) && $_GET['page'] === 'mslms_zoom_reports') {
    require_once MSLMS_ZOOM_PATH . '/src/Layouts/backend/reports.php';
}
else if(!empty($_GET['page']) && $_GET['page'] === 'mslms_zoom_assign_host_id') {
    require_once MSLMS_ZOOM_PATH . '/src/Layouts/backend/assign-host.php';
}
else {
	wp_redirect( admin_url( 'admin.php?page=mslms_zoom_users' ) );
	exit;
}