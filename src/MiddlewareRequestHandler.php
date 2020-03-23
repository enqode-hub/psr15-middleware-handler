<?php declare(strict_types=1);

namespace ApiKit\Middleware;

use Psr\Http\Message\{ ServerRequestInterface, ResponseInterface };
use Psr\Http\Server\{ RequestHandlerInterface, MiddlewareInterface };

class MiddlewareRequestHandler implements RequestHandlerInterface {

  /**
   * @var MiddlewareInterface[]
   */
  private $middlewares = [];

  /**
   * @param MiddlewareInterface $middleware
   * @return MiddlewareRequestHandler
   */
  public function add(MiddlewareInterface $middleware): MiddlewareRequestHandler{
    $this->middlewares[] = $middleware;
    return $this;
  }

  /**
   * @param ServerRequestInterface $request
   * @return ResponseInterface
   */
  public function handle(ServerRequestInterface $request): ResponseInterface {
    return $this->handleRecursive($request, 0);
  }

  /**
   * @param ServerRequestInterface $request
   * @param integer $index
   * @return ResponseInterface
   */
  private function handleRecursive(ServerRequestInterface $request, int $index = 0): ResponseInterface {
    $next = $this->createRequestHandler(fn($request) => $this->handleRecursive($request, $index + 1));
    return $this->middlewares[$index]->process($request, $next);
  }

  /**
   * @param callable $handle
   * @return RequestHandlerInterface
   */
  private function createRequestHandler(callable $handle): RequestHandlerInterface{
    return new class ($handle) implements RequestHandlerInterface {

      /**
       * @var callable
       */
      private $handle;

      /**
       * @param callable $handle
       */
      public function __construct($handle){
        $this->handle = $handle;
      }

      /**
       * @param ServerRequestInterface $request
       * @return ResponseInterface
       */
      public function handle(ServerRequestInterface $request): ResponseInterface {
        return call_user_func($this->handle, $request);
      }

    };
  }

}