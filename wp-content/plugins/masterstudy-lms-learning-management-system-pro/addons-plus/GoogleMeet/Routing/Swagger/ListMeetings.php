<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

final class ListMeetings extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'start'   => array(
				'type'        => 'integer',
				'description' => 'Meetings offset start',
			),
			'length'  => array(
				'type'        => 'integer',
				'description' => 'Meetings per page',
			),
			'order'   => array(
				'type'        => 'array',
				'description' => 'Meetings ordered by',
			),
			'columns' => array(
				'type'        => 'array',
				'description' => 'Meetings table columns',
			),
		);
	}

	public function response(): array {
		return array(
			'data'            => array(
				'type'  => 'array',
				'items' => array(),
			),
			'recordsTotal'    => array( 'type' => 'integer' ),
			'recordsFiltered' => array( 'type' => 'integer' ),
		);
	}

	public function get_summary(): string {
		return 'Get Meetings list';
	}

	public function get_description(): string {
		return 'Returns a list of meetings for the instructor.';
	}
}
