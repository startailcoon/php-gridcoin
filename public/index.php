<?php

namespace phpGridcoin;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Wallet.php';
require __DIR__ . '/HttpErrorHandler.php';
require __DIR__ . '/ShutdownHandler.php';


use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;
use Slim\Handlers\Strategies\RequestHandler;


use phpGridcoin\ErrorHandlers\HttpErrorHandler;
use phpGridcoin\ErrorHandlers\ShutdownHandler;
use phpGridcoin\Wallet;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;

/**
 * Setup the application
 * ------------------------------------
 * Slim App and register the error handlers.
 * Wallet connection settings.
 * 
 */

// Instantiate Custom Error Handler
// This should be disabled in production
$displayErrorDetails = true;

// Set Wallet Node Connection Settings
Wallet::setNode('localhost', '25717', 'gridcoinrpc', 'bkw75QgtWAAQpnU0MHR4qIQIfAqXR7OxdvHPHI6xI4VMQKXXEkpfPo2dT');

// End of Setup
// ------------------------------------

// Create AppFactory
$app = AppFactory::create();

// Set Middleware Error Handler
// https://www.slimframework.com/docs/v4/objects/application.html

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Handling Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Setup Routes
// ------------------------------------
// https://www.slimframework.com/docs/v4/objects/routing.html#how-to-create-routes
//
// Error Routes to Use
// ------------------------
// HttpBadRequestException is the most common to use when the request
// can not be filled due to a bad request. 
// 
// HttpBadRequestException      -- 400, Request is invalid or not found
// HttpUnauthorizedException    -- 401, Authentication is required but not supplied
// HttpForbiddenException       -- 403, Authentication is supplied but not allowed
// HttpNotFoundException        -- 404, Resource is not found
// HttpMethodNotAllowedException-- 405, Method is not allowed
// HttpException                -- 500, An internal error has occurred while processing your request.
// HttpNotImplementedException  -- 501, Method is not implemented

foreach(glob(__DIR__ . '/routes/*.php') as $route) {
    require $route;
}

// End of Routes
// ------------------------------------
// Run the application

$app->run();


?>