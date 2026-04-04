<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkUpdate extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'action'  => array(
				'type'        => 'string',
				'description' => 'Bulk action to perform on coupons',
				'enum'        => array( 'delete', 'trash', 'active', 'inactive' ),
				'required'    => true,
			),
			'coupons' => array(
				'type'        => 'array',
				'description' => 'Array of coupons to perform the bulk action on',
				'required'    => true,
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id' => array(
							'type'        => 'integer',
							'description' => 'Coupon ID',
							'required'    => true,
						),
					),
					'required'   => array( 'id' ),
				),
			),
		);
	}

	public function response(): array {
		return array(
			'success'     => array(
				'type'        => 'boolean',
				'description' => 'Whether the bulk action was overall successful',
			),
			'message'     => array(
				'type'        => 'string',
				'description' => 'Summary message about the bulk action',
			),
			'success_ids' => array(
				'type'        => 'array',
				'description' => 'IDs of coupons that were successfully processed',
				'items'       => array(
					'type' => 'integer',
				),
			),
			'failed'      => array(
				'type'        => 'array',
				'description' => 'List of coupons that failed to update with error messages',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'      => array(
							'type'        => 'integer',
							'description' => 'Coupon ID',
						),
						'message' => array(
							'type'        => 'string',
							'description' => 'Error message for this coupon',
						),
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Bulk update coupons';
	}

	public function get_description(): string {
		return 'Perform bulk operations on multiple coupons (delete, move to trash, activate, deactivate).';
	}
}
