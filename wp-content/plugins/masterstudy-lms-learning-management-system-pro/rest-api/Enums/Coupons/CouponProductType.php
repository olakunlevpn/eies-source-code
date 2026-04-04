<?php
namespace MasterStudy\Lms\Pro\RestApi\Enums\Coupons;

use MasterStudy\Lms\Enums\Enum;

final class CouponProductType extends Enum {

	public const FULL_SITE                 = 'full-site';
	public const ALL_COURSES               = 'all-courses';
	public const ALL_BUNDLES               = 'all-bundles';
	public const ALL_COURSES_BUNDLES       = 'all-courses-bundles';
	public const ALL_MEMBERSHIP_PLANS      = 'all-membership-plans';
	public const SPECIFIC_COURSES          = 'specific-courses';
	public const SPECIFIC_BUNDLES          = 'specific-bundles';
	public const SPECIFIC_CATEGORIES       = 'specific-category';
	public const SPECIFIC_MEMBERSHIP_PLANS = 'specific-membership-plans';


	public static function get_translate_options(): array {
		return array(
			self::FULL_SITE                 => esc_html__( 'All courses, bundles and memberships', 'masterstudy-lms-learning-management-system-pro' ),
			self::ALL_COURSES               => esc_html__( 'All courses', 'masterstudy-lms-learning-management-system-pro' ),
			self::ALL_BUNDLES               => esc_html__( 'All bundles', 'masterstudy-lms-learning-management-system-pro' ),
			self::ALL_COURSES_BUNDLES       => esc_html__( 'All courses bundles', 'masterstudy-lms-learning-management-system-pro' ),
			self::ALL_MEMBERSHIP_PLANS      => esc_html__( 'All membership plans', 'masterstudy-lms-learning-management-system-pro' ),
			self::SPECIFIC_COURSES          => esc_html__( 'Specific courses', 'masterstudy-lms-learning-management-system-pro' ),
			self::SPECIFIC_BUNDLES          => esc_html__( 'Specific bundles', 'masterstudy-lms-learning-management-system-pro' ),
			self::SPECIFIC_CATEGORIES       => esc_html__( 'Specific categories', 'masterstudy-lms-learning-management-system-pro' ),
			self::SPECIFIC_MEMBERSHIP_PLANS => esc_html__( 'Specific membership plans', 'masterstudy-lms-learning-management-system-pro' ),
		);
	}
}
