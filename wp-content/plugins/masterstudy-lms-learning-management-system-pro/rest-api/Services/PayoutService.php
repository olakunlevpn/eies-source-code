<?php

namespace MasterStudy\Lms\Pro\RestApi\Services;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use STM_LMS_Options;
use stmLms\Classes\Models\StmStatistics;
use stmLms\Classes\Models\StmUser;

class PayoutService {

	public function get_payout_revenue( array $params ): array {
		$user = new StmUser( get_current_user_id() );

		$data                    = array( 'author_id' => $user->ID );
		$data['paypal_email']    = get_user_meta( $user->ID, 'stm_lms_paypal_email', true );
		$data['currency_symbol'] = STM_LMS_Options::get_option( 'currency_symbol', '$' );

		$param_start = $params['date_start'] ?? $params['date_from'] ?? null;
		$param_end   = $params['date_end'] ?? $params['date_to'] ?? null;

		try {
			if ( ! empty( $param_start ) ) {
				$date_start = new DateTime( (string) $param_start );
			} else {
				$date_start = new DateTime( 'first day of this month' );
				$date_start->modify( '-5 months' );
			}

			if ( ! empty( $param_end ) ) {
				$date_end = new DateTime( (string) $param_end );
			} else {
				$date_end = new DateTime( 'now' );
			}
		} catch ( Exception $e ) {
			$date_end   = new DateTime( 'now' );
			$date_start = ( clone $date_end )->modify( 'first day of this month' )->modify( '-5 months' );
		}

		$date_start->modify( 'first day of this month' )->setTime( 0, 0, 0 );
		$date_end->modify( 'last day of this month' )->setTime( 23, 59, 59 );

		if ( $date_start > $date_end ) {
			$tmp        = $date_start;
			$date_start = $date_end;
			$date_end   = $tmp;
		}

		$labels_earnings = array();
		$months_keys     = array();

		$interval = DateInterval::createFromDateString( '1 month' );

		$end_exclusive = ( clone $date_end )->modify( 'first day of next month' )->setTime( 0, 0, 0 );

		$period = new DatePeriod( clone $date_start, $interval, $end_exclusive );

		foreach ( $period as $dt ) {
			$labels_earnings[] = $dt->format( 'M Y' );
			$months_keys[]     = $dt->format( 'Y-m' );
		}

		if ( ! empty( $params['course_id'] ) ) {
			$user_courses = $user->get_course_by_id( (int) $params['course_id'] );
		} else {
			$user_courses = $user->get_courses();
		}

		$datasets_earnings = array();
		$only_one_month    = 1 === count( $months_keys );

		foreach ( $user_courses as $k => $user_course ) {
			if ( ! is_object( $user_course ) || empty( $user_course->ID ) ) {
				continue;
			}

			$courses = StmStatistics::get_course_statisticas(
				$date_start->format( 'Y-m-d' ),
				$date_end->format( 'Y-m-d' ),
				$user->ID,
				$user_course->ID
			);

			$datasets_data = array_fill( 0, count( $months_keys ), 0 );

			$title = $user_course->title ?? $user_course->post_title ?? 'Course';
			$color = 'rgba(161, 61, 76, 0.5)';

			foreach ( $courses as $course ) {
				$course_date = new DateTime( $course['date'] );
				$key         = $course_date->format( 'Y-m' );

				$idx = array_search( $key, $months_keys, true );
				if ( false !== $idx ) {
					$datasets_data[ $idx ] += (float) $course['amount'];
				}

				$title = $course['title'];
				$color = $course['backgroundColor'];
			}

			if ( $only_one_month ) {
				$datasets_data[1]  = $datasets_data[0];
				$months_keys[]     = $months_keys[0] . '-dup';
				$labels_earnings[] = $labels_earnings[0] . ' ';
			}

			$datasets_earnings[ $k ] = array(
				'label'           => $title,
				'backgroundColor' => $color,
				'data'            => $datasets_data,
			);
		}

		$data['labels_earnings']   = $labels_earnings;
		$data['datasets_earnings'] = array_values( $datasets_earnings );

		$data['sales_statistics'] = StmStatistics::get_course_sales_statisticas( $user->ID );

		return $data;
	}
}
