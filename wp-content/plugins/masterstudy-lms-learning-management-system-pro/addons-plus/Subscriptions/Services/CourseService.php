<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Services;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\SubscriptionPlanType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanItemRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;

class CourseService {
	public function has_access_to_course( $user_id, $course, $course_id, $add ) {
		$course               = is_array( $course ) && isset( $course[0] ) && is_array( $course[0] ) ? $course[0] : $course;
		$author_id            = get_post_field( 'post_author', $course_id );
		$in_enterprise        = \STM_LMS_Order::is_purchased_by_enterprise( $course, $user_id );
		$my_course            = intval( $author_id ) === intval( $user_id );
		$is_free              = ( get_post_meta( $course_id, 'single_sale', true ) && empty( \STM_LMS_Course::get_course_price( $course_id ) ) );
		$is_bought            = \STM_LMS_Order::has_purchased_courses( $user_id, $course_id );
		$in_bundle            = ! empty( $course['bundle_id'] );
		$bought_by_membership = ! empty( $course['subscription_id'] );
		$for_points           = ! empty( $course['for_points'] );
		$not_in_membership    = get_post_meta( $course_id, 'not_membership', true );
		$only_for_membership  = ! $is_bought && ! $is_free && ! $in_enterprise && ! $in_bundle && ! $for_points && ! $my_course;

		// Get user active subscriptions
		$has_access      = false;
		$subscription_id = ! empty( $course['subscription_id'] ) ? intval( $course['subscription_id'] ) : 0;

		if ( ! $only_for_membership ) {
			return array(
				'has_access'           => $has_access,
				'only_for_membership'  => $only_for_membership,
				'subscription_id'      => $subscription_id,
				'bought_by_membership' => $bought_by_membership,
			);
		}

		$user_subscriptions = ( new SubscriptionRepository() )->get_active_subscriptions_by_user( $user_id );
		if ( ! $user_subscriptions ) {
			return array(
				'has_access'           => $has_access,
				'only_for_membership'  => $only_for_membership,
				'subscription_id'      => $subscription_id,
				'bought_by_membership' => $bought_by_membership,
			);
		}

		// Check user access to course
		foreach ( $user_subscriptions as $subscription ) {
			$subscription_plan = ( new SubscriptionPlanRepository() )->get( $subscription['plan_id'] );

			if ( ! $subscription_plan ) {
				continue;
			}

			// Full site access
			if ( SubscriptionPlanType::FULL_SITE === $subscription_plan['type'] && ! $not_in_membership ) {
				$subscription_id = $subscription['plan_id'];
				$has_access      = true;
				break;
			}

			// Category access
			if ( SubscriptionPlanType::CATEGORY === $subscription_plan['type'] && ! $not_in_membership ) {
				$course_categories       = stm_lms_get_terms_array( $course_id, 'stm_lms_course_taxonomy', 'term_id' );
				$subscription_object_ids = array_column( $subscription_plan['items'], 'object_id' );
				$subscription_object_ids = array_map( 'intval', $subscription_object_ids );
				$common_ids              = array_intersect( $course_categories, $subscription_object_ids );

				if ( ! empty( $common_ids ) ) {
					$has_access      = true;
					$subscription_id = $subscription['plan_id'];
					break;
				}
			}

			// Course access
			if ( SubscriptionPlanType::COURSE === $subscription_plan['type'] ) {
				$subscription_course_ids = array_column( $subscription_plan['items'], 'object_id' );
				$subscription_course_ids = array_map( 'intval', $subscription_course_ids );

				if ( in_array( $course_id, $subscription_course_ids, true ) ) {
					$has_access      = true;
					$subscription_id = $subscription['plan_id'];
					break;
				}
			}
		}

		if ( $only_for_membership && ! $bought_by_membership && ! empty( $subscription_id ) && $add ) {
			\STM_LMS_Course::add_user_course( $course_id, $user_id, 0, 0, false, '', '', '', '', $subscription_id );
			$bought_by_membership = true;
		}

		return array(
			'has_access'           => $has_access,
			'only_for_membership'  => $only_for_membership,
			'subscription_id'      => $subscription_id,
			'bought_by_membership' => $bought_by_membership,
		);
	}

	public function get_course_access_state( $user_id, $course, $course_id ) {
		$course               = is_array( $course ) && isset( $course[0] ) && is_array( $course[0] ) ? $course[0] : $course;
		$author_id            = get_post_field( 'post_author', $course_id );
		$in_enterprise        = \STM_LMS_Order::is_purchased_by_enterprise( $course, $user_id );
		$my_course            = intval( $author_id ) === intval( $user_id );
		$is_free              = ( get_post_meta( $course_id, 'single_sale', true ) && empty( \STM_LMS_Course::get_course_price( $course_id ) ) );
		$is_bought            = \STM_LMS_Order::has_purchased_courses( $user_id, $course_id );
		$in_bundle            = ! empty( $course['bundle_id'] );
		$bought_by_membership = ! empty( $course['subscription_id'] );
		$for_points           = ! empty( $course['for_points'] );
		$not_in_membership    = get_post_meta( $course_id, 'not_membership', true );
		$only_for_membership  = ! $not_in_membership && ! $is_bought && ! $is_free && ! $in_enterprise && ! $in_bundle && ! $for_points && ! $my_course && $bought_by_membership;

		if ( ! $only_for_membership ) {
			return array(
				'membership_expired'  => false,
				'membership_inactive' => false,
				'no_membership_plan'  => false,
			);
		}

		$subs = ( new SubscriptionRepository() )->get_user_subscriptions_with_plans( $user_id );

		if ( empty( $subs ) ) {
			return array(
				'membership_expired'  => false,
				'membership_inactive' => false,
				'no_membership_plan'  => true,
			);
		}

		$plan_ids       = array_unique( array_map( 'intval', array_column( $subs, 'plan_id' ) ) );
		$items_by_plan  = ( new SubscriptionPlanItemRepository() )->get_by_plan_ids( $plan_ids );
		$course_cat_ids = array_map( 'intval', (array) stm_lms_get_terms_array( $course_id, 'stm_lms_course_taxonomy', 'term_id' ) );
		$matched        = array();

		foreach ( $subs as $sub ) {
			$type  = $sub['plan_type'];
			$items = $items_by_plan[ (int) $sub['plan_id'] ] ?? array();

			if ( SubscriptionPlanType::FULL_SITE === $type ) {
				$matched[] = $sub;
				continue;
			}

			if ( SubscriptionPlanType::CATEGORY === $type ) {
				$ids = array_map( 'intval', array_column( $items, 'object_id' ) );
				if ( array_intersect( $course_cat_ids, $ids ) ) {
					$matched[] = $sub;
				}
				continue;
			}

			if ( SubscriptionPlanType::COURSE === $type ) {
				$ids = array_map( 'intval', array_column( $items, 'object_id' ) );
				if ( in_array( (int) $course_id, $ids, true ) ) {
					$matched[] = $sub;
				}
			}
		}

		if ( empty( $matched ) ) {
			return array(
				'membership_expired'  => false,
				'membership_inactive' => false,
				'no_membership_plan'  => true,
			);
		}

		$has_active    = false;
		$has_expired   = false;
		$has_cancelled = false;

		foreach ( $matched as $sub ) {
			$status = strtolower( (string) ( $sub['status'] ?? '' ) );

			if ( 'active' === $status ) {
				$has_active = true;
				break;
			} elseif ( 'expired' === $status ) {
				$has_expired = true;
			} elseif ( 'cancelled' === $status ) {
				$has_cancelled = true;
			}
		}

		$membership_expired  = false;
		$membership_inactive = false;

		if ( $has_active ) {
			$membership_expired  = false;
			$membership_inactive = false;
		} elseif ( $has_expired ) {
			$membership_expired = true;
		} elseif ( $has_cancelled ) {
			$membership_inactive = true;
		}

		return array(
			'membership_expired'  => $membership_expired,
			'membership_inactive' => $membership_inactive,
			'no_membership_plan'  => false,
		);
	}

	public function is_certificate_provided( $user_id, $course_id ): bool {
		$course = stm_lms_get_user_course( $user_id, $course_id, array( 'subscription_id', 'bundle_id', 'for_points', 'enterprise_id' ) );
		$course = is_array( $course ) && isset( $course[0] ) && is_array( $course[0] ) ? $course[0] : (array) $course;

		$author_id                       = get_post_field( 'post_author', $course_id );
		$in_enterprise                   = \STM_LMS_Order::is_purchased_by_enterprise( $course, $user_id );
		$enterprise_do_not_provide_cert  = get_post_meta( $course_id, 'enterprise_do_not_provide_certificate', true );
		$my_course                       = intval( $author_id ) === intval( $user_id );
		$pricing_mode                    = get_post_meta( $course_id, 'pricing_mode', true );
		$is_free                         = (
			class_exists( '\MasterStudy\Lms\Enums\PricingMode' )
				? ( \MasterStudy\Lms\Enums\PricingMode::FREE === $pricing_mode )
				: (
					get_post_meta( $course_id, 'single_sale', true )
					&& empty( \STM_LMS_Course::get_course_price( $course_id ) )
				)
		);
		$free_do_not_provide_cert        = get_post_meta( $course_id, 'free_do_not_provide_certificate', true );
		$single_sale_do_not_provide_cert = get_post_meta( $course_id, 'single_sale_do_not_provide_certificate', true );
		$in_bundle                       = ! empty( $course['bundle_id'] );
		$bought_by_membership            = ! empty( $course['subscription_id'] );
		$for_points                      = ! empty( $course['for_points'] );
		$points_do_not_provide_cert      = get_post_meta( $course_id, 'points_do_not_provide_certificate', true );
		$not_in_membership               = get_post_meta( $course_id, 'not_membership', true );
		$membership_do_not_provide_cert  = get_post_meta( $course_id, 'membership_do_not_provide_certificate', true );
		$user_subscriptions              = ( new SubscriptionRepository() )->get_active_subscriptions_by_user( $user_id );
		$is_bought                       = \STM_LMS_Order::has_purchased_courses( $user_id, $course_id ) && ! $in_enterprise && ! $for_points && ! $is_free;
		$only_for_membership             = ! $is_bought && ! $is_free && ! $in_enterprise && ! $in_bundle && ! $for_points && ! $my_course;

		if (
			( $in_enterprise && 'on' === $enterprise_do_not_provide_cert ) ||
			( $for_points && 'on' === $points_do_not_provide_cert ) ||
			( $is_bought && 'on' === $single_sale_do_not_provide_cert ) ||
			( $is_free && 'on' === $free_do_not_provide_cert )
		) {
			return false;
		}

		if ( empty( $user_subscriptions ) && ! $bought_by_membership ) {
			return true;
		}

		if ( $bought_by_membership && ! $only_for_membership ) {
			return true;
		}

		$course_cat_ids       = array_map( 'intval', (array) stm_lms_get_terms_array( $course_id, 'stm_lms_course_taxonomy', 'term_id' ) );
		$any_match_cert_true  = false;
		$any_match_cert_false = false;
		$has_any_match        = false;
		$matched_cert_type    = '';

		foreach ( (array) $user_subscriptions as $subscription ) {
			$plan = ( new SubscriptionPlanRepository() )->get( (int) $subscription['plan_id'] );
			if ( empty( $plan ) ) {
				continue;
			}

			$type               = $plan['type'] ?? null;
			$items              = isset( $plan['items'] ) && is_array( $plan['items'] ) ? $plan['items'] : array();
			$plan_covers_course = false;

			if ( SubscriptionPlanType::FULL_SITE === $type && ! $not_in_membership ) {
				$plan_covers_course = true;
			}

			if ( ! $plan_covers_course && SubscriptionPlanType::CATEGORY === $type && ! $not_in_membership ) {
				$ids = array_map( 'intval', array_column( $items, 'object_id' ) );
				if ( ! empty( array_intersect( $course_cat_ids, $ids ) ) ) {
					$plan_covers_course = true;
				}
			}

			if ( ! $plan_covers_course && SubscriptionPlanType::COURSE === $type ) {
				$ids = array_map( 'intval', array_column( $items, 'object_id' ) );
				if ( in_array( (int) $course_id, $ids, true ) ) {
					$plan_covers_course = true;
				}
			}

			if ( $plan_covers_course ) {
				$has_any_match = true;

				if ( array_key_exists( 'is_certified', $plan ) && (bool) $plan['is_certified'] ) {
					$any_match_cert_true = true;
					$matched_cert_type   = $type;
					break;
				} else {
					$any_match_cert_false = true;
				}
			}
		}

		if ( $any_match_cert_true ) {
			if (
					$matched_cert_type &&
					in_array( $matched_cert_type, array( SubscriptionPlanType::FULL_SITE, SubscriptionPlanType::CATEGORY ), true ) &&
					'on' === $membership_do_not_provide_cert
			) {
				return false;
			}

			return true;
		}

		if ( $has_any_match && $any_match_cert_false ) {
			return false;
		}

		if ( $bought_by_membership ) {
			return false;
		}

		return true;
	}
}
