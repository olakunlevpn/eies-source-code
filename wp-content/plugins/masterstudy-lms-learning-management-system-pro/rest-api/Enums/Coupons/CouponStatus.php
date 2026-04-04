<?php
namespace MasterStudy\Lms\Pro\RestApi\Enums\Coupons;

use MasterStudy\Lms\Enums\Enum;

final class CouponStatus extends Enum {

	public const ACTIVE   = 'active';
	public const INACTIVE = 'inactive';
	public const TRASH    = 'trash';

	public static function get_translate_options(): array {
		return array(
			self::ACTIVE   => esc_html__( 'Active', 'masterstudy-lms-learning-management-system-pro' ),
			self::INACTIVE => esc_html__( 'Inactive', 'masterstudy-lms-learning-management-system-pro' ),
			self::TRASH    => esc_html__( 'Trash', 'masterstudy-lms-learning-management-system-pro' ),
		);
	}
}
