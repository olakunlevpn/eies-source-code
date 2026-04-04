<?php
namespace MasterStudy\Lms\Pro\RestApi\Enums\Coupons;

use MasterStudy\Lms\Enums\Enum;

final class CouponType extends Enum {

	public const COUPON        = 'coupon';
	public const AUTO_DISCOUNT = 'auto-discount';

	public static function get_translate_options(): array {
		return array(
			self::AUTO_DISCOUNT => esc_html__( 'Automatic Discount', 'masterstudy-lms-learning-management-system-pro' ),
			self::COUPON        => esc_html__( 'Coupon', 'masterstudy-lms-learning-management-system-pro' ),
		);
	}
}
