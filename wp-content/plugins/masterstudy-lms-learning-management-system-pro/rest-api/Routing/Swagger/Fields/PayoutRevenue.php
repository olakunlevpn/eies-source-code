<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

class PayoutRevenue extends Field {
	/**
	 * Object Properties
	 */
	public static array $properties = array(
		'datasets_earnings' => array(
			'type' => 'array',
		),
		'labels_earnings'   => array(
			'type' => 'array',
		),
	);
}
