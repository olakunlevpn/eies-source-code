<?php

namespace MasterStudy\Lms\Pro\RestApi\Enums\Coupons;

use MasterStudy\Lms\Enums\Enum;

final class BulkCouponAction extends Enum {
	public const DELETE   = 'delete';
	public const ACTIVE   = 'update_status_active';
	public const INACTIVE = 'update_status_inactive';
	public const TRASH    = 'update_status_trash';
}
