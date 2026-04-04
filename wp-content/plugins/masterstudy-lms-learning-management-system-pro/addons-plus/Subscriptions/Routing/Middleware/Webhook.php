<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Middleware;

use MasterStudy\Lms\Routing\MiddlewareInterface;

class Webhook implements MiddlewareInterface {
	public function process( $request, callable $next ) {
		return $next( $request );
	}
}
