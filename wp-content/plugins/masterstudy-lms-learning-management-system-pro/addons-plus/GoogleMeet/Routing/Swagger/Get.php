<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

final class Get extends Route implements ResponseInterface {

	public function response(): array {
		return array(
			'meeting' => array(
				'type'        => 'object',
				'description' => 'Google Meeting',
				'properties'  => array(
					'id'         => array(
						'type'        => 'integer',
						'description' => 'Meeting ID',
					),
					'title'      => array(
						'type'        => 'string',
						'description' => 'Meeting title',
					),
					'summary'    => array(
						'type'        => 'string',
						'description' => 'Meeting Summary',
						'required'    => false,
					),
					'start_date' => array(
						'type'        => 'integer',
						'description' => 'Meeting Start Date',
					),
					'start_time' => array(
						'type'        => 'string',
						'description' => 'Meeting Start Time',
					),
					'end_date'   => array(
						'type'        => 'integer',
						'description' => 'Meeting End Date',
					),
					'end_time'   => array(
						'type'        => 'string',
						'description' => 'Meeting End Time',
					),
					'timezone'   => array(
						'type'        => 'string',
						'description' => 'Meeting Timezone',
					),
					'attendees'  => array(
						'type'        => 'boolean',
						'description' => 'Enrolled Students as Attendees',
						'required'    => false,
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Get Google Meeting';
	}

	public function get_description(): string {
		return 'Returns Google Meeting';
	}
}
