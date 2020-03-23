<?php declare(strict_types=1);

use ApiKit\Middleware\{ MiddlewareFactory, MiddlewareRequestHandler };

require __DIR__ . '/../vendor/autoload.php';

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$response = (new \Laminas\Diactoros\ResponseFactory)->createResponse();

$middlewareFactory = new MiddlewareFactory();

$middlewareRequestHandler = new MiddlewareRequestHandler();
$middlewareRequestHandler->add($middlewareFactory->fromCallable(fn($req, $next) => $next->handle($req)));
$middlewareRequestHandler->add($middlewareFactory->fromCallable(fn() => $response));

// run
$response = $middlewareRequestHandler->handle($request);
