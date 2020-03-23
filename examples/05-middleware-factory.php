<?php declare(strict_types=1);

use ApiKit\Middleware\{ MiddlewareFactory };
use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\{ ResponseInterface, ServerRequestInterface, ResponseFactoryInterface };
use Psr\Http\Server\RequestHandlerInterface;

require __DIR__ . '/../vendor/autoload.php';

$middlewareFactory = new MiddlewareFactory();

// create a middleware from a callable
$middleware = $middlewareFactory->fromCallable(function(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface {
  return $next->handle($request);
});

// create a middleware from a response factory (ResponseFactoryInterface)
$middleware = $middlewareFactory->fromResponseFactory(new ResponseFactory);

// create a middleware from a response object (ResponseInterface)
$middleware = $middlewareFactory->fromResponse((new ResponseFactory)->createResponse());
