<?php declare(strict_types=1);

namespace ApiKit\Middleware;

use ApiKit\Middleware\MiddlewareRequestHandler;
use Psr\Http\Message\{ ResponseInterface, ServerRequestInterface };
use Psr\Http\Server\{ MiddlewareInterface, RequestHandlerInterface };

class MiddlewareRequestHandlerMiddleware implements MiddlewareInterface {

  /**
   * @var MiddlewareRequestHandler
   */
  private $middlewareRequestHandler;

  /**
   * @param MiddlewareRequestHandler $middlewareRequestHandler
   */
  public function __construct(MiddlewareRequestHandler $middlewareRequestHandler){
    $this->middlewareRequestHandler = $middlewareRequestHandler;
  }

  /**
   * @param ServerRequestInterface $request
   * @param RequestHandlerInterface $next
   * @return ResponseInterface
   */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface {
    $middlewareRequestHandler = clone $this->middlewareRequestHandler;
    $middlewareFactory = new MiddlewareFactory();
    $middlewareRequestHandler->add($middlewareFactory->fromCallable(fn($req) => $next->handle($req)));
    return $middlewareRequestHandler->handle($request);
  }

}