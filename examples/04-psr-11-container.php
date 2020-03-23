<?php declare(strict_types=1);

use ApiKit\Middleware\{ MiddlewareContainer, MiddlewareFactory, MiddlewareRequestHandler };
use ApiKit\Middleware\Tests\Mock\{ Container, Middleware };

require __DIR__ . '/../vendor/autoload.php';

$container = new Container([
  Middleware::class => fn() => new Middleware(),
]);

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$response = (new \Laminas\Diactoros\ResponseFactory)->createResponse();

$middlewareFactory = new MiddlewareFactory();

$middlewareRequestHandler = new MiddlewareRequestHandler();
$middlewareRequestHandler->add(new MiddlewareContainer($container, Middleware::class));
$middlewareRequestHandler->add(new MiddlewareContainer($container, Middleware::class));
$middlewareRequestHandler->add($middlewareFactory->fromCallable(fn() => $response));

// run
$response = $middlewareRequestHandler->handle($request);
