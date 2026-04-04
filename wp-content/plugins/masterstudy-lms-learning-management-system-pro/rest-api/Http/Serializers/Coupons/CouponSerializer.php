<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Coupons;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;
use STM_LMS_Course;

final class CouponSerializer extends AbstractSerializer {

	public function toArray( $data ): array {
		if ( $data instanceof \stdClass ) {
			$data = (array) $data;
		}

		$date_format = get_option( 'date_format' ) ?? 'Y-m-d';
		$time_format = get_option( 'time_format' ) ?? 'H:i';

		$items = null;
		if ( ! empty( $data['items'] ) ) {
			$decoded = json_decode( $data['items'], true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				$items = $decoded;
			}
		}

		$start_at_raw = $data['start_at'] ?? null;
		$end_at_raw   = $data['end_at'] ?? null;

		$start_ts = null;
		$end_ts   = null;

		$timezone = function_exists( 'wp_timezone' )
			? wp_timezone()
			: new \DateTimeZone( wp_timezone_string() );

		if ( $start_at_raw ) {
			$start_dt = \DateTime::createFromFormat( 'Y-m-d H:i:s', $start_at_raw, $timezone );
			if ( $start_dt instanceof \DateTime ) {
				$start_ts = $start_dt->getTimestamp();
			}
		}

		if ( $end_at_raw ) {
			$end_dt = \DateTime::createFromFormat( 'Y-m-d H:i:s', $end_at_raw, $timezone );
			if ( $end_dt instanceof \DateTime ) {
				$end_ts = $end_dt->getTimestamp();
			}
		}

		$usage_limit = array_key_exists( 'usage_limit', $data ) && null !== $data['usage_limit']
			? (int) $data['usage_limit']
			: null;

		$used_count = isset( $data['used_count'] ) ? (int) $data['used_count'] : 0;

		$usage_remaining = null;
		if ( null !== $usage_limit ) {
			$usage_remaining = max( 0, $usage_limit - $used_count );
		}

		$raw_status = isset( $data['coupon_status'] ) ? (string) $data['coupon_status'] : '';
		$is_active  = ( 'active' === $raw_status );
		$is_expired = false;

		$now    = current_datetime();
		$now_ts = (int) $now->getTimestamp();

		if ( $is_active && $start_ts && $now_ts < $start_ts ) {
			$is_active = false;
		}

		if ( $is_active && $end_ts && $now_ts > $end_ts ) {
			$is_active  = false;
			$is_expired = true;
		}

		$status = $raw_status;

		$start_date_ms = null;
		$end_date_ms   = null;

		$start_time_str = null;
		$end_time_str   = null;

		$start_at_human = null;
		$end_at_human   = null;

		if ( $start_ts ) {
			$start_at_human = wp_date( "{$date_format} {$time_format}", $start_ts );
			$start_time_str = wp_date( 'H:i', $start_ts );
			$start_date_str = wp_date( $date_format, $start_ts );
			$start_date_dt  = \DateTime::createFromFormat( $date_format, $start_date_str, $timezone );
			if ( $start_date_dt instanceof \DateTime ) {
				$start_date_dt->setTime( 0, 0, 0 );
				$start_date_ms = (int) ( $start_date_dt->getTimestamp() * 1000 );
			}
		}

		if ( $end_ts ) {
			$end_at_human = wp_date( "{$date_format} {$time_format}", $end_ts );
			$end_time_str = wp_date( 'H:i', $end_ts );
			$end_date_str = wp_date( $date_format, $end_ts );
			$end_date_dt  = \DateTime::createFromFormat( $date_format, $end_date_str, $timezone );
			if ( $end_date_dt instanceof \DateTime ) {
				$end_date_dt->setTime( 0, 0, 0 );
				$end_date_ms = (int) ( $end_date_dt->getTimestamp() * 1000 );
			}
		}

		$product_type = (string) ( $data['product_type'] ?? 'all' );
		$detail_items = $this->build_detail_items( $product_type, $items );

		return array(
			'id'                  => isset( $data['id'] ) ? (int) $data['id'] : null,
			'title'               => (string) ( $data['title'] ?? '' ),
			'coupon_status'       => (string) $status,
			'code'                => (string) ( $data['code'] ?? '' ),
			'discount_type'       => (string) ( $data['discount_type'] ?? 'percent' ),
			'discount'            => isset( $data['discount'] ) ? (float) $data['discount'] : 0.0,
			'discount_formatted'  => 'percent' === $data['discount_type'] ? (float) $data['discount'] . '%' : \STM_LMS_Helpers::display_price( $data['discount'] ),
			'product_type'        => $product_type,
			'usage_limit'         => (int) $usage_limit,
			'used_count'          => (int) $used_count,
			'usage_remaining'     => (int) $usage_remaining,
			'user_usage_limit'    => array_key_exists( 'user_usage_limit', $data ) && null !== $data['user_usage_limit']
				? (int) $data['user_usage_limit']
				: null,
			'min_purchase_amount' => array_key_exists( 'min_purchase_amount', $data ) && null !== $data['min_purchase_amount']
				? (float) $data['min_purchase_amount']
				: null,
			'min_course_quantity' => array_key_exists( 'min_course_quantity', $data ) && null !== $data['min_course_quantity']
				? (int) $data['min_course_quantity']
				: null,
			'start_date'          => $start_date_ms,
			'end_date'            => $end_date_ms,
			'start_time'          => $start_time_str,
			'end_time'            => $end_time_str,
			'start_at'            => $start_at_human,
			'end_at'              => $end_at_human,
			'items'               => $items,
			'detail_items'        => $detail_items,
			'is_active'           => $is_active,
			'is_expired'          => $is_expired,
		);
	}

	protected function build_detail_items( string $product_type, ?array $items ): array {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return array();
		}

		$ids          = array_map( 'intval', $items );
		$detail_items = array();

		switch ( $product_type ) {
			case 'specific-courses':
				foreach ( $ids as $id ) {

					$course = ( new CourseRepository() )->find( $id, 'grid' );

					if ( empty( $course ) ) {
						continue;
					}

					$raw_price      = property_exists( $course, 'price' ) ? $course->price : null;
					$raw_sale_price = property_exists( $course, 'sale_price' ) ? $course->sale_price : null;

					$price      = ( null !== $raw_price && '' !== $raw_price )
						? \STM_LMS_Helpers::display_price( (float) $raw_price )
						: null;
					$sale_price = ( null !== $raw_sale_price && '' !== $raw_sale_price )
						? \STM_LMS_Helpers::display_price( (float) $raw_sale_price )
						: null;

					$detail_items[] = array(
						'object_id'   => $id,
						'object_type' => 'course',
						'link'        => get_the_permalink( $id ),
						'object'      => array(
							'name'       => $course->title ?? '',
							'price'      => $price,
							'sale_price' => $sale_price,
							'image_url'  => $course->thumbnail['url'] ?? '',
						),
					);
				}
				break;

			case 'specific-bundles':
				foreach ( $ids as $id ) {

					$bundle = ( new CourseBundleRepository() )->get_bundle_data( $id );

					if ( empty( $bundle ) ) {
						continue;
					}

					$raw_price = property_exists( $bundle, 'bundle_price' ) ? $bundle->bundle_price : null;
					$price     = ( null !== $raw_price && '' !== $raw_price )
						? \STM_LMS_Helpers::display_price( (float) $raw_price )
						: null;

					$detail_items[] = array(
						'object_id'   => $id,
						'object_type' => 'bundle',
						'link'        => get_permalink( $id ),
						'object'      => array(
							'name'      => $bundle->bundle_title ?? '',
							'price'     => $price,
							'image_url' => $bundle->bundle_thumbnail_url ?? '',
						),
					);
				}
				break;

			case 'specific-categories':
				foreach ( $ids as $id ) {
					$term = get_term( $id, 'stm_lms_course_taxonomy' );
					if ( ! $term || is_wp_error( $term ) ) {
						continue;
					}

					$detail_items[] = array(
						'object_id'   => (int) $term->term_id,
						'object_type' => 'category',
						'link'        => esc_url( STM_LMS_Course::courses_page_url() ) . "?terms[]=$term->term_id&category[]=$term->term_id",
						'object'      => array(
							'name'      => $term->name,
							'price'     => null,
							'image_url' => '',
						),
					);
				}
				break;

			case 'specific-membership-plans':
				if ( is_ms_lms_addon_enabled( 'subscriptions' ) ) {
					$repo = new SubscriptionPlanRepository();

					foreach ( $ids as $id ) {
						if ( $id <= 0 ) {
							continue;
						}

						$plan = $repo->get( $id );
						if ( empty( $plan ) || ! is_array( $plan ) ) {
							continue;
						}

						$name  = (string) $plan['name'] ?? '';
						$price = isset( $plan['price'] ) ? \STM_LMS_Helpers::display_price( (float) $plan['price'] ) : null;

						$detail_items[] = array(
							'object_id'   => $id,
							'object_type' => 'membership',
							'link'        => admin_url( "admin.php?page=manage_membership_plans&plan_id=$id" ),
							'object'      => array(
								'name'      => $name,
								'price'     => $price,
								'image_url' => '',
							),
						);
					}
				}
				break;

			default:
				break;
		}

		return $detail_items;
	}
}
