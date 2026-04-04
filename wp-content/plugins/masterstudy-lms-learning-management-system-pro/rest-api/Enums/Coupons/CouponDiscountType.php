<?php
namespace MasterStudy\Lms\Pro\RestApi\Enums\Coupons;

use MasterStudy\Lms\Enums\Enum;

final class CouponDiscountType extends Enum {

	public const PERCENT = 'percent';
	public const AMOUNT  = 'amount';

	public static function get_translate_options(): array {
		return array(
			self::PERCENT => esc_html__( 'Percent', 'masterstudy-lms-learning-management-system-pro' ),
			self::AMOUNT  => esc_html__( 'Amount', 'masterstudy-lms-learning-management-system-pro' ),
		);
	}
}
