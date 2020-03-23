<?php declare(strict_types=1);

namespace ApiKit\Middleware\Tests\Mock;

use Psr\Http\Message\{ ResponseInterface, ServerRequestInterface };
use Psr\Http\Server\{ MiddlewareInterface, RequestHandlerInterface };

class Middleware implements MiddlewareInterface {

  /**
   * @param ServerRequestInterface $request
   * @param RequestHandlerInterface $next
   * @return ResponseInterface
   */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface {
    return $next->handle($request);
  }

}