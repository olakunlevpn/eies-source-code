<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums;

use MasterStudy\Lms\Enums\Enum;

/**
 * @method static self Hour()
 * @method static self Day()
 * @method static self Week()
 * @method static self Month()
 * @method static self Year()
 */
class ReccuringInterval extends Enum {
	public const DAY   = 'day';
	public const WEEK  = 'week';
	public const MONTH = 'month';
	public const YEAR  = 'year';

	public static function get_translate_options(): array {
		return array(
			self::MONTH => esc_html__( 'Month', 'masterstudy-lms-learning-management-system-pro' ),
			self::YEAR  => esc_html__( 'Year', 'masterstudy-lms-learning-management-system-pro' ),
			self::WEEK  => esc_html__( 'Week', 'masterstudy-lms-learning-management-system-pro' ),
			self::DAY   => esc_html__( 'Day', 'masterstudy-lms-learning-management-system-pro' ),
		);
	}
}
