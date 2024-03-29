<?php

namespace CoonDesign\phpGridcoin;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// Detect if the script is in a composer vendor directory
if(stristr(__DIR__, 'vendor') !== false) {
    require __DIR__ . '/../../../../../../vendor/autoload.php';
} else {
    require __DIR__ . '/../../../../vendor/autoload.php';
}

require __DIR__ . '/../../../src/Wallet.php';
require __DIR__ . '/HttpErrorHandler.php';
require __DIR__ . '/ShutdownHandler.php';
require __DIR__ . '/HttpRateLimitException.php';

use CoonDesign\RateLimit\RateLimitMiddleware;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

use CoonDesign\phpGridcoin\ErrorHandlers\HttpErrorHandler;
use CoonDesign\phpGridcoin\ErrorHandlers\ShutdownHandler;
use CoonDesign\phpGridcoin\Wallet;

/**
 * Setup the application
 * ------------------------------------
 * Slim App and register the error handlers.
 * Wallet connection settings.
 * 
 */

// Instantiate Custom Error Handler
// This should be disabled in production
$displayErrorDetails = false;

// Set Wallet Node Connection Settings
Wallet::setNode('localhost', '25717', 'gridcoinrpc', 'bkw75QgtWAAQpnU0MHR4qIQIfAqXR7OxdvHPHI6xI4VMQKXXEkpfPo2dT');

$basePath = '/wallet/v1';

// End of Setup
// ------------------------------------

// Create AppFactory
$app = AppFactory::create();
$app->setBasePath($basePath);

// Add Rate Limit Middleware
// Limits per requester IP address, x requests per y seconds
// Default is 30 requests per 60 seconds

$rateLimitMiddleware = new RateLimitMiddleware();
$rateLimitMiddleware->setRequestsPerSecond(30, 60);
$rateLimitMiddleware->setHandler(function ($request) {
    throw new Exception\HttpRateLimitException($request, 'Rate limit exceeded. Slow down.');
});

$app->add($rateLimitMiddleware);

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


// Load Requested Route
$route = explode("/", str_replace("$basePath/", '', $_SERVER['REDIRECT_URL']));
$route_file = realpath(__DIR__) . '/routes/' . $route[0] . '.php';

if(!file_exists($route_file)) {
    $app->get('/{routes:.+}', function (ServerRequestInterface $request, ResponseInterface $response) {
        throw new HttpNotFoundException($request, 'Requested route not found');
    });
} else {
    require $route_file;
}

// Run the application
$app->run();

?>