<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Interfaces;

interface WebhookInterface {
	public function handle_webhook( $payload, $headers ): void;
}
