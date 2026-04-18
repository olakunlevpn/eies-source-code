<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'eies_customizations_options' );

$types = array( 'years', 'courses', 'students', 'instructors' );
foreach ( $types as $t ) {
	delete_transient( 'eies_stat_' . $t );
	delete_option( 'eies_stat_' . $t . '_last_good' );
}
