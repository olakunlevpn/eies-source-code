<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

// Webhooks
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Middleware\Webhook::class,
		),
	),
	function ( Router $router ) {
		$router->post(
			'/ecommerce-webhook/{payment_method}',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\WebhookController::class,
		);
	}
);

// Subscription Plans
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Middleware\Instructor::class,
		),
		'prefix'     => '/subscription-plan',
	),
	function ( Router $router ) {
		$router->get(
			'/list',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan\ListController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan\PlansList::class,
		);
		$router->get(
			'/{plan_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan\GetController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan\Get::class,
		);
		$router->post(
			'/create',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan\CreateController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan\Create::class,
		);
		$router->put(
			'/{plan_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan\UpdateController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan\Update::class,
		);
		$router->post(
			'/reorder',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan\ReorderController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan\Reorder::class,
		);
		$router->put(
			'/{plan_id}/toggle-enabled',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan\ToggleEnabledController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan\ToggleEnabled::class,
		);
		$router->delete(
			'/{plan_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan\DeleteController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan\Delete::class,
		);
	}
);

// Subscriptions
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Middleware\Instructor::class,
		),
		'prefix'     => '/subscription',
	),
	function ( Router $router ) {
		$router->get(
			'/list',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\ListController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\ListRoute::class,
		);
		$router->get(
			'/{subscription_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\GetController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\Get::class,
		);
		$router->post(
			'/{subscription_id}/payment-history',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\PaymentHistoryController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\PaymentHistory::class
		);
		$router->post(
			'/{subscription_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\UpdateController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\Update::class
		);
		$router->put(
			'/{subscription_id}/cancel',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\CancelController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\Cancel::class
		);
		$router->post(
			'/bulk-update',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\BulkUpdateSubscriptionsController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\BulkUpdate::class
		);
	}
);

// User Subscriptions
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Middleware\SubscriptionOwner::class,
		),
		'prefix'     => '/my-subscription',
	),
	function ( Router $router ) {
		$router->post(
			'/list',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\ListController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\ListRoute::class,
		);
		$router->get(
			'/{subscription_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\GetController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\Get::class,
		);
		$router->post(
			'/{subscription_id}/payment-history',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\PaymentHistoryController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\PaymentHistory::class
		);
		$router->put(
			'/{subscription_id}/cancel',
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription\CancelController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription\Cancel::class
		);
	}
);
