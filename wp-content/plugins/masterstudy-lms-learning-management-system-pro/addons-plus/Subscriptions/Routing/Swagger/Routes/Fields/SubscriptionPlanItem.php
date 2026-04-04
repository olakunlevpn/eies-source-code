<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

class SubscriptionPlanItem extends Field {
	/**
	 * Object Properties
	 */
	public static array $properties = array(
		'object_type' => array(
			'type'        => 'string',
			'enum'        => array( 'full_site', 'category', 'course' ),
			'required'    => true,
			'description' => 'Object type',
		),
		'object_id'   => array(
			'type'        => 'integer',
			'description' => 'Related Object ID',
			'required'    => true,
		),
	);
}
