<?php

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;

function masterstudy_lms_course_bundle_order_approved( $order_item, $user_id ) {
	if ( ! empty( $order_item->get_meta( '_bundle_id' ) ) ) {
		$bundle_id = intval( $order_item->get_meta( '_bundle_id' ) );
		$courses   = CourseBundleRepository::get_bundle_courses( $bundle_id );

		if ( ! empty( $courses ) ) {
			foreach ( $courses as $course_id ) {
				if ( 'stm-courses' === get_post_type( $course_id ) ) {
					STM_LMS_Course::add_user_course(
						$course_id,
						$user_id,
						0,
						0,
						false,
						'',
						$bundle_id
					);
					STM_LMS_Course::add_student( $course_id );
				}
			}
		}
	}
}
add_action( 'stm_lms_woocommerce_order_approved', 'masterstudy_lms_course_bundle_order_approved', 10, 2 );

function masterstudy_lms_course_bundle_order_cancelled( $order_item, $user_id ) {
	if ( ! empty( $order_item->get_meta( '_bundle_id' ) ) ) {
		$bundle_id = intval( $order_item->get_meta( '_bundle_id' ) );

		if ( ! STM_LMS_Woocommerce::has_course_been_purchased( $user_id, $bundle_id ) ) {
			$bundle_courses = CourseBundleRepository::get_bundle_courses( $bundle_id );

			if ( ! empty( $bundle_courses ) ) {
				global $wpdb;

				foreach ( $bundle_courses as $id ) {
					$wpdb->delete(
						stm_lms_user_courses_name( $wpdb ),
						array(
							'user_id'   => $user_id,
							'course_id' => $id,
							'bundle_id' => $bundle_id,
						)
					);
				}
			}
		}
	}
}
add_action( 'stm_lms_woocommerce_order_cancelled', 'masterstudy_lms_course_bundle_order_cancelled', 10, 2 );

function course_bundle_create_order_line_item( $item, $values, $order ) {
	if ( ! empty( $values['bundle_id'] ) && $order instanceof WC_Order ) {
		$item->update_meta_data( '_bundle_id', $values['bundle_id'] );
	}
}

add_action( 'stm_lms_create_order_line_item', 'course_bundle_create_order_line_item', 10, 3 );
