<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Update extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'text' => array(
				'type'        => 'string',
				'description' => 'Note text to add to the subscription',
				'required'    => true,
				'maxLength'   => 1000,
			),
		);
	}

	public function response(): array {
		return array(
			'result' => array(
				'type'        => 'boolean',
				'description' => 'result of updated row for subscription',
			),
		);
	}

	public function get_summary(): string {
		return 'Add or update subscription note';
	}

	public function get_description(): string {
		return 'Add or update a note for a specific subscription. The note will be stored in the subscription record and can be used for internal comments or administrative purposes.';
	}
}
