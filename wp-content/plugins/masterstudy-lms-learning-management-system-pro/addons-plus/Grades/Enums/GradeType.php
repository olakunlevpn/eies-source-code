<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Enums;

use MasterStudy\Lms\Enums\Enum;

/**
 * @method static self Grade()
 * @method static self Point()
 * @method static self Percent()
 */
final class GradeType extends Enum {
	public const GRADE   = 'grade';
	public const POINT   = 'point';
	public const PERCENT = 'percent';
}
