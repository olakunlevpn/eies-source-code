<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums;

use MasterStudy\Lms\Enums\Enum;

/**
 * @method static self FullSite()
 * @method static self Category()
 * @method static self Course()
 */
class SubscriptionPlanType extends Enum {
	public const FULL_SITE = 'full_site';
	public const CATEGORY  = 'category';
	public const COURSE    = 'course';
	public const BUNDLE    = 'bundle';
}
