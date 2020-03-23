<?php declare(strict_types=1);

namespace ApiKit\Middleware\Tests\Unit;

use ApiKit\Middleware\{ MiddlewareFactory, MiddlewareRequestHandler, MiddlewareRequestHandlerMiddleware };
use Laminas\Diactoros\{ ServerRequestFactory, ResponseFactory };

final class MiddlewareRequestHandlerMiddlewareTest extends \PHPUnit\Framework\TestCase {

  /**
   * @var MiddlewareFactory
   */
  private $middlewareFactory;

  /**
   * @return void
   */
  public function setUp(): void {
    $this->middlewareFactory = new MiddlewareFactory();
  }

  /**
   * This test makes sure that the middlewares get called in the correct order.
   * 
   * @return void
   */
  public function testCallOrder(){
    $request = ServerRequestFactory::fromGlobals();
    $response = (new ResponseFactory)->createResponse();

    $order = [];
    $push = function($n, $req, $next) use(&$order){
      $order[] = $n;
      $res = $next->handle($req);
      $order[] = $n;
      return $res;
    };

    $innerMiddlewareRequestHandler = new MiddlewareRequestHandler();
    $innerMiddlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $push('2.1', $req, $next)));
    $innerMiddlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $push('2.2', $req, $next)));

    $outerMiddlewareRequestHandler = new MiddlewareRequestHandler();
    $outerMiddlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $push('1', $req, $next)));
    $outerMiddlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $push('2', $req, $next)));
    $outerMiddlewareRequestHandler->add(new MiddlewareRequestHandlerMiddleware($innerMiddlewareRequestHandler));
    $outerMiddlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $push('3', $req, $next)));

    $outerMiddlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn() => $response));
    $outerMiddlewareRequestHandler->handle($request);

    $this->assertEquals(['1', '2', '2.1', '2.2', '3', '3', '2.2', '2.1', '2', '1'], $order, 'Actual order: ' . json_encode($order));
  }

}