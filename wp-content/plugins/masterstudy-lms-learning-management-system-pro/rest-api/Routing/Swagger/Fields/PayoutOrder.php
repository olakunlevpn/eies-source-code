<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

class PayoutOrder extends Field {
	/**
	 * Object Properties
	 */
	public static array $properties = array(
		'id'                     => array(
			'type' => 'integer',
		),
		'date_created'           => array(
			'type' => 'string',
		),
		'date_created_formatted' => array(
			'type' => 'string',
		),
		'amount'                 => array(
			'type' => 'integer',
		),
		'amount_formatted'       => array(
			'type' => 'string',
		),
		'status'                 => array(
			'type' => 'string',
			'enum' => array( 'completed', 'pending', 'canceled' ),
		),
		'method'                 => array(
			'type'        => 'string',
			'description' => 'Payment method',
		),
	);
}
