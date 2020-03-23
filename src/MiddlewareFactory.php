<?php declare(strict_types=1);

namespace ApiKit\Middleware;

use Psr\Http\Message\{ ResponseFactoryInterface, ResponseInterface, ServerRequestInterface };
use Psr\Http\Server\{ MiddlewareInterface, RequestHandlerInterface };

class MiddlewareFactory {

  /**
   * @param callable $handle
   * @return MiddlewareInterface
   */
  public function fromCallable(callable $handle): MiddlewareInterface {
    return new class ($handle) implements MiddlewareInterface{

      /**
       * @var callable
       */
      private $handle;

      /**
       * @param callable $handle
       */
      public function __construct(callable $handle){
        $this->handle = $handle;        
      }

      /**
       * @param ServerRequestInterface $request
       * @param RequestHandlerInterface $next
       * @return ResponseInterface
       */
      public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface {
        return call_user_func($this->handle, $request, $next);
      }

    };
  }

  /**
   * @param ResponseFactoryInterface $response
   * @return MiddlewareInterface
   */
  public function fromResponseFactory(ResponseFactoryInterface $responseFactory): MiddlewareInterface {
    return $this->fromCallable(fn() => $responseFactory->createResponse());
  }

  /**
   * @param ResponseInterface $response
   * @return MiddlewareInterface
   */
  public function fromResponse(ResponseInterface $response) {
    return $this->fromCallable(fn() => $response);
  }

}