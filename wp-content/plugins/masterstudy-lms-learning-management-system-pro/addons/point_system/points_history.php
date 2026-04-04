<?php

new STM_LMS_Point_History();

class STM_LMS_Point_History {

	public function __construct() {
		add_action( 'wp_ajax_stm_lms_get_user_points_history', array( $this, 'get_user_points_history' ) );
	}

	public function get_user_points_history() {
		check_ajax_referer( 'stm_lms_get_user_points_history', 'nonce' );

		wp_send_json( self::get_user_points() );
	}

	public static function per_row() {
		return 10;
	}

	public static function points( $user_id = '' ) {
		$user    = STM_LMS_User::get_current_user( $user_id );
		$user_id = $user['id'];

		$per_row = self::per_row();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$offset = ( ! empty( $_GET['page'] ) ) ? ( intval( $_GET['page'] ) * $per_row ) - $per_row : 0;

		$results     = stm_lms_get_user_points_history( $user_id, $per_row, $offset );
		$points      = $results['result'];
		$total       = $results['total'];
		$points_info = stm_lms_point_system();

		foreach ( $points as &$point ) {
			$point['timestamp'] = stm_lms_time_elapsed_string( gmdate( 'Y-m-d H:i:s', $point['timestamp'] ) );
			$point['data']      = $points_info[ $point['action_id'] ];
			if ( $point['score'] > 0 ) {
				$point['score'] = "+{$point['score']}";
			}

			switch ( $point['action_id'] ) {
				case 'user_registered':
				case 'user_registered_affiliate':
				case 'course_purchased_affiliate':
					$user           = STM_LMS_User::get_current_user( $point['user_id'] );
					$affiliate_id   = get_user_meta( $point['user_id'], 'affiliate_id', true );
					$affiliate_name = $affiliate_id ? get_the_author_meta( 'display_name', $affiliate_id ) : $user['login'];
					$point['title'] = $affiliate_name;
					$point['url']   = STM_LMS_User::student_public_page_url( $point['user_id'] );
					break;
				case 'group_joined':
					$point['title'] = bp_get_group_name( groups_get_group( $point['id'] ) );
					$point['url']   = bp_get_group_permalink( groups_get_group( $point['id'] ) );
					break;
				default:
					$point['title'] = get_the_title( $point['id'] );
					$point['url']   = get_the_permalink( $point['id'] );
			}
		}

		ob_start();
		foreach ( $points as $point ) {
			STM_LMS_Templates::show_lms_template( 'points/card', array( 'point' => $point ) );
		}
		$html = ob_get_clean();

		ob_start();
		if ( ! empty( $points ) && $total > 1 ) {
			STM_LMS_Templates::show_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $total_pages,
					'current_page'      => $page,
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
				)
			);
		}
		$pagination = ob_get_clean();

		return(
			array(
				'html'         => $html,
				'pagination'   => $pagination,
				'total_pages'  => $total_pages,
				'current_page' => $page,
			)
		);
	}

	public static function get_user_points( $user_id = '' ) {
		$user    = ! empty( $user_id ) ? STM_LMS_User::get_current_user( $user_id ) : STM_LMS_User::get_current_user( get_current_user_id() );
		$user_id = ! empty( $user['id'] ) ? absint( $user['id'] ) : 0;

		$per_row = absint( self::per_row() );
		$page    = ! empty( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
		$offset  = ( $page - 1 ) * $per_row;

		$results     = stm_lms_get_user_points_history( $user_id, $per_row, $offset );
		$points      = ! empty( $results['result'] ) && is_array( $results['result'] ) ? $results['result'] : array();
		$total       = isset( $results['total'] ) ? absint( $results['total'] ) : 0;
		$points_info = stm_lms_point_system();
		$total_pages = ( $total > 0 ) ? (int) ceil( $total / $per_row ) : 1;

		foreach ( $points as &$point ) {
			$ts                 = isset( $point['timestamp'] ) ? absint( $point['timestamp'] ) : 0;
			$point['timestamp'] = $ts ? stm_lms_time_elapsed_string( gmdate( 'Y-m-d H:i:s', $ts ) ) : '';

			$action_id     = isset( $point['action_id'] ) ? $point['action_id'] : '';
			$point['data'] = isset( $points_info[ $action_id ] ) ? $points_info[ $action_id ] : array( 'label' => '' );

			$score = isset( $point['score'] ) ? (int) $point['score'] : 0;

			if ( $score > 0 ) {
				$point['score'] = '+' . $score;
			}

			switch ( $action_id ) {
				case 'user_registered':
				case 'user_registered_affiliate':
				case 'course_purchased_affiliate':
					$u              = STM_LMS_User::get_current_user( $point['user_id'] ?? 0 );
					$affiliate_id   = get_user_meta( $point['user_id'] ?? 0, 'affiliate_id', true );
					$affiliate_name = $affiliate_id ? get_the_author_meta( 'display_name', $affiliate_id ) : ( $u['login'] ?? '' );

					$point['title'] = $affiliate_name;
					$point['url']   = STM_LMS_User::student_public_page_url( $point['user_id'] ?? 0 );
					break;

				case 'group_joined':
					if ( function_exists( 'groups_get_group' ) && function_exists( 'bp_get_group_name' ) && function_exists( 'bp_get_group_permalink' ) ) {
						$group          = groups_get_group( $point['id'] ?? 0 );
						$point['title'] = bp_get_group_name( $group );
						$point['url']   = bp_get_group_permalink( $group );
					} else {
						$point['title'] = '';
						$point['url']   = '';
					}
					break;

				default:
					$pid            = isset( $point['id'] ) ? absint( $point['id'] ) : 0;
					$point['title'] = $pid ? get_the_title( $pid ) : '';
					$point['url']   = $pid ? get_the_permalink( $pid ) : '';
			}
		}
		unset( $point );

		ob_start();
		foreach ( $points as $point ) {
			STM_LMS_Templates::show_lms_template( 'points/card', array( 'point' => $point ) );
		}
		$html = ob_get_clean();

		ob_start();
		if ( ! empty( $points ) && $total_pages > 1 ) {
			STM_LMS_Templates::show_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $total_pages,
					'current_page'      => $page,
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
					'thin'              => true,
				)
			);
		}
		$pagination = ob_get_clean();

		return array(
			'posts'        => $points,
			'html'         => $html,
			'pagination'   => $pagination,
			'total_pages'  => $total_pages,
			'current_page' => $page,
		);
	}
}
