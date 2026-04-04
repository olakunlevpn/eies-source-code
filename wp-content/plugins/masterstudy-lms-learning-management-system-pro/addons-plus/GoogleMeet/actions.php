<?php

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleCalendarEvent;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleOpenAuth;
use MasterStudy\Lms\Repositories\CurriculumRepository;

add_action(
	'init',
	function () {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['scope'] ) || ! isset( $_GET['code'] ) ) {
			return;
		}

		$current_url  = home_url( $_SERVER['REQUEST_URI'] );
		$exclude_urls = array( 'page=google_classrooms', 'page=bookit-staff', 'addon=social_login' );

		foreach ( $exclude_urls as $exclude_url ) {
			if ( strpos( $current_url, $exclude_url ) !== false ) {
				return;
			}
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		( new GoogleOpenAuth() )->process_auth_code( $_GET['code'] );
	}
);

add_action( 'save_post', array( GoogleCalendarEvent::class, 'save_google_meeting' ), 10, 3 );

function masterstudy_lms_google_meet_admin_calendar_field() {
	global $pagenow, $post_type;

	$post_date_value = '';
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'edit.php' === $pagenow && 'stm-google-meets' === $post_type && isset( $_GET['wp_admin_custom_meet_date'] ) && '' !== $_GET['wp_admin_custom_meet_date'] ) {
		$post_date_value = $_GET['wp_admin_custom_meet_date']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	$post_type = 'post';
	if ( isset( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_type = $_GET['post_type']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( 'stm-google-meets' === $post_type ) {
		echo ' <input type="date" id="wp_admin_custom_meet_date" name="wp_admin_custom_meet_date"';
		if ( '' !== $post_date_value ) {
			echo 'value="' . esc_html( $post_date_value ) . '"';
		}
		echo '>';
	}
}
add_action( 'restrict_manage_posts', 'masterstudy_lms_google_meet_admin_calendar_field' );

function masterstudy_lms_google_meet_admin_calendar_filter( $query ) {
	global $pagenow;

	$post_type = 'post';
	if ( isset( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_type = $_GET['post_type']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( 'edit.php' === $pagenow && $query->is_main_query() && 'stm-google-meets' === $post_type ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['wp_admin_custom_meet_date'] ) && '' !== $_GET['wp_admin_custom_meet_date'] ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$custom_date           = sanitize_text_field( $_GET['wp_admin_custom_meet_date'] );
			$custom_date_timestamp = strtotime( $custom_date ) * 1000;
			$meta_query            = array(
				'relation' => 'AND',
				array(
					'key'     => 'stm_gma_end_date',
					'value'   => $custom_date_timestamp,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				),
				array(
					'key'     => 'stm_gma_start_date',
					'value'   => $custom_date_timestamp,
					'compare' => '<=',
					'type'    => 'NUMERIC',
				),
			);
			$query->set( 'meta_query', $meta_query );
		}
	}
}
add_action( 'pre_get_posts', 'masterstudy_lms_google_meet_admin_calendar_filter' );

function masterstudy_lms_google_meet_admin_scripts() {
	wp_enqueue_style( 'stm-google-meet-css', STM_LMS_URL . '/assets/css/parts/google_meet/meet-admin.css', array(), STM_LMS_PRO_VERSION );
}
add_action( 'admin_enqueue_scripts', 'masterstudy_lms_google_meet_admin_scripts' );

/**
 * Add Google Meet custom columns to admin table
 */
function masterstudy_lms_google_meet_admin_custom_columns( $columns, $post_id ) {
	$current_post_metas = get_post_meta( $post_id );
	$is_meet_started    = masterstudy_lms_is_google_meet_started( $post_id );

	switch ( $columns ) {
		case 'gm_date':
			$start_date = ! empty( $current_post_metas['stm_gma_start_date'][0] )
				? gmdate( 'j F Y', $current_post_metas['stm_gma_start_date'][0] / 1000 )
				: '';
			$end_date   = ! empty( $current_post_metas['stm_gma_end_date'][0] )
				? gmdate( 'j F Y', $current_post_metas['stm_gma_end_date'][0] / 1000 )
				: '';
			$start_time = $current_post_metas['stm_gma_start_time'][0] ?? '';
			$end_time   = $current_post_metas['stm_gma_end_time'][0] ?? '';
			echo esc_html( $start_date . ' - ' . $start_time ) . '<br/>' . esc_html( $end_date . ' - ' . $end_time );
			break;
		case 'gm_meeting_url':
			$meet_link = $current_post_metas['google_meet_link'][0] ?? '';
			echo '<a href="' . esc_url( $meet_link ) . '">' . esc_html( $meet_link ) . '</a>';
			break;
		case 'gm_host':
			echo esc_html( get_the_author_meta( 'user_email', get_post_field( 'post_author', $post_id ) ) );
			break;
		case 'gm_actions':
			?>
			<div>
				<?php
				if ( $is_meet_started ) {
					?>
					<a class=" button-primary meet-links expired-meeting" disabled><?php echo esc_html( 'Expired' ); ?></a>
					<?php
				} else {
					?>
					<a href="<?php echo esc_url( $current_post_metas['google_meet_link'][0] ?? '' ); ?>" class="button button-primary meet-links"><?php echo esc_html( 'Start meeting' ); ?></a>
					<a href="<?php echo esc_url( admin_url() . 'post.php?post=' . $post_id . '&action=edit' ); ?>" class="btn button components-button is-tertiary meet-links"><?php echo esc_html( 'Edit' ); ?></a>
					<?php
				}
				?>
				<a href="<?php echo esc_url( get_delete_post_link( $post_id ) ); ?>" class="btn button button-secondary meet-links ">
					<div class="meet-link-trash"><img src="<?php echo esc_url( STM_LMS_PRO_URL . 'assets/img/trash_meet.png' ); ?>" alt=""></div>
				</a>
			</div>
			<?php
			break;
	}
}
add_filter( 'manage_stm-google-meets_posts_custom_column', 'masterstudy_lms_google_meet_admin_custom_columns', 10, 2 );

function masterstudy_lms_add_google_meet_attendee( $user_id, $course_id ) {
	$course_curriculum = ( new CurriculumRepository() )->get_curriculum( $course_id );
	$column            = array_column( $course_curriculum['materials'], 'post_type' );

	if ( in_array( PostType::GOOGLE_MEET, $column, true ) ) {
		$course_meets = array_filter(
			$course_curriculum['materials'],
			function ( $item ) {
				return isset( $item['post_type'] ) && PostType::GOOGLE_MEET === $item['post_type'];
			}
		);

		foreach ( $course_meets as $item ) {
			if ( ! empty( get_post_meta( $item['post_id'], 'stm_gma_attendees', true ) ) ) {
				$meet_id   = get_post_meta( $item['post_id'], 'google_meet_id' );
				$user_data = get_userdata( $user_id );

				if ( ! empty( $meet_id && $user_data ) ) {

					// Add new attendees
					$new_attendees = array(
						new Google_Service_Calendar_EventAttendee( array( 'email' => $user_data->user_email ) ),
					);
					GoogleCalendarEvent::add_users_to_event( $new_attendees, $item['post_id'] );
				}
			}
		}
	}
}
add_action( 'add_user_course', 'masterstudy_lms_add_google_meet_attendee', 10, 2 );
