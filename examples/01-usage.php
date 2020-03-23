<?php declare(strict_types=1);

use ApiKit\Middleware\{ MiddlewareFactory, MiddlewareRequestHandler };
use Psr\Http\Message\{ ServerRequestInterface, ResponseInterface };
use Psr\Http\Server\MiddlewareInterface;

require __DIR__ . '/../vendor/autoload.php';

$middlewareRequestHandler = new MiddlewareRequestHandler();
$middlewareRequestHandler->add( /* instanceof MiddlewareInterface */ );
$middlewareRequestHandler->add( /* instanceof MiddlewareInterface */ );
$middlewareRequestHandler->add( /* instanceof MiddlewareInterface */ );
$middlewareRequestHandler->add((new MiddlewareFactory)->fromResponse( /* instanceof ResponseInterface */ ));

$response = $middlewareRequestHandler->handle( /* instanceof ServerRequestInterface */ );
