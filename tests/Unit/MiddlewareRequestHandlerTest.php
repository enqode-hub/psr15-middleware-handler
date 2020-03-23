<?php declare(strict_types=1);

namespace ApiKit\Middleware\Tests\Unit;

use ApiKit\Middleware\{ MiddlewareFactory, MiddlewareRequestHandler };
use Laminas\Diactoros\{ ServerRequestFactory, ResponseFactory };

final class MiddlewareRequestHandlerTest extends \PHPUnit\Framework\TestCase {

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
   * This test makes sure that teh response will be passed through the whole
   * middleware pipe. The response is created by the last / most outer middleware.
   * 
   *  M1 -> M2 -> M3 -> {M4} -> M3 -> M2 -> M1 => response
   * 
   * @return void
   */
  public function testPassingResponseThroughPipe(){
    $middlewareRequestHandler = new MiddlewareRequestHandler();

    $request = ServerRequestFactory::fromGlobals();
    $response = (new ResponseFactory)->createResponse();

    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $next->handle($req)));
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $next->handle($req)));
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $next->handle($req)));
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn() => $response));

    $returnedResponse = $middlewareRequestHandler->handle($request);

    $this->assertEquals($response, $returnedResponse);
  }

  /**
   * This test makes sure that the modified request is passed to the next middleware.
   * 
   * @return void
   */
  public function testPassingModifiedRequestToNextMiddleware(){
    $middlewareRequestHandler = new MiddlewareRequestHandler();

    $request = ServerRequestFactory::fromGlobals();
    $response = (new ResponseFactory)->createResponse();

    $asserted = false;

    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $next->handle($req)));
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(function($req, $next){
      $req = $req->withAttribute('label', 'yeah');
      return $next->handle($req);
    }));
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(function($req, $next) use(&$asserted){
      $this->assertEquals($req->getAttribute('label'), 'yeah');
      $asserted = true;
      return $next->handle($req);
    }));
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn() => $response));
    $middlewareRequestHandler->handle($request);

    $this->assertTrue($asserted); // check that assert was actually called
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

    $middlewareRequestHandler = new MiddlewareRequestHandler();
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $push('1', $req, $next)));
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $push('2', $req, $next)));
    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(fn($req, $next) => $push('3', $req, $next)));

    $middlewareRequestHandler->add($this->middlewareFactory->fromCallable(function() use(&$order, $response){
      $order[] = 4;
      return $response;
    }));
    $middlewareRequestHandler->handle($request);

    $this->assertEquals(['1', '2', '3', '4', '3', '2', '1'], $order, 'Actual order: ' . json_encode($order));
  }

}