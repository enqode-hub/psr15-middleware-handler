<?php declare(strict_types=1);

use ApiKit\Middleware\{ MiddlewareFactory, MiddlewareRequestHandler, MiddlewareRequestHandlerMiddleware };

require __DIR__ . '/../vendor/autoload.php';

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$response = (new \Laminas\Diactoros\ResponseFactory)->createResponse();

$middlewareFactory = new MiddlewareFactory();

// create the inner handler
$innerMiddlewareRequestHandler = new MiddlewareRequestHandler();
$innerMiddlewareRequestHandler->add($middlewareFactory->fromCallable(fn($req, $next) => $next->handle($req)));
$innerMiddlewareRequestHandler->add($middlewareFactory->fromCallable(fn($req, $next) => $next->handle($req)));

// create the outer handler
$middlewareRequestHandler = new MiddlewareRequestHandler();
$middlewareRequestHandler->add($middlewareFactory->fromCallable(fn($req, $next) => $next->handle($req)));
$middlewareRequestHandler->add(new MiddlewareRequestHandlerMiddleware($innerMiddlewareRequestHandler));
$middlewareRequestHandler->add($middlewareFactory->fromCallable(function() use($response){
  return $response;
}));

// run
$response = $middlewareRequestHandler->handle($request);
