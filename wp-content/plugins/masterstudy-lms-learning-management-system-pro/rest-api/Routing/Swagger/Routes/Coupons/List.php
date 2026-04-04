<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\CouponSchema;

class ListRoute extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'length'    => array(
				'type'        => 'integer',
				'description' => 'Number of records to return (page size)',
				'required'    => false,
				'default'     => 10,
			),
			'start'     => array(
				'type'        => 'integer',
				'description' => 'Offset of the first record (for pagination)',
				'required'    => false,
				'default'     => 0,
			),
			'status'    => array(
				'type'        => 'string',
				'description' => 'Filter coupons by status',
				'enum'        => array( 'active', 'inactive', 'trash', 'expired' ),
				'required'    => false,
			),
			'date_from' => array(
				'type'        => 'string',
				'format'      => 'date',
				'description' => 'Filter coupons created from this date (YYYY-MM-DD)',
				'required'    => false,
			),
			'date_to'   => array(
				'type'        => 'string',
				'format'      => 'date',
				'description' => 'Filter coupons created up to this date (YYYY-MM-DD)',
				'required'    => false,
			),
			'search'    => array(
				'type'        => 'object',
				'description' => 'Search options (DataTables style)',
				'required'    => false,
				'properties'  => array(
					'value' => array(
						'type'        => 'string',
						'description' => 'Search term to match against coupon title and code',
					),
				),
			),
			'order'     => array(
				'type'        => 'array',
				'description' => 'Sorting configuration (DataTables style)',
				'required'    => false,
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'column' => array(
							'type'        => 'integer',
							'description' => 'Index of the column to sort by',
						),
						'dir'    => array(
							'type'        => 'string',
							'description' => 'Sort direction',
							'enum'        => array( 'asc', 'desc' ),
						),
					),
				),
			),
			'columns'   => array(
				'type'        => 'array',
				'description' => 'Column metadata (DataTables style), used for sorting',
				'required'    => false,
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'data' => array(
							'type'        => 'string',
							'description' => 'Column data key (e.g. "coupon_status", "start_date")',
						),
					),
				),
			),
		);
	}

	public function response(): array {
		return array(
			'data'            => array(
				'type'        => 'array',
				'description' => 'List of coupons',
				'items'       => array(
					'type'       => 'object',
					'properties' => CouponSchema::coupon_properties(),
				),
			),
			'recordsTotal'    => array(
				'type'        => 'integer',
				'description' => 'Total number of coupons in the system',
			),
			'recordsFiltered' => array(
				'type'        => 'integer',
				'description' => 'Total number of coupons after applying filters/search',
			),
		);
	}

	public function get_summary(): string {
		return 'List coupons';
	}

	public function get_description(): string {
		return 'Get a paginated, filterable list of coupons (DataTables compatible response).';
	}
}
