<?php declare(strict_types=1);

namespace ApiKit\Middleware\Tests\Unit;

use ApiKit\Middleware\MiddlewareFactory;
use ApiKit\Middleware\Tests\Mock\RequestHandler;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;

final class MiddlewareFactoryTest extends \PHPUnit\Framework\TestCase {

  /**
   * This test makes sure the passed function will be executed when processing a middleware.
   *
   * @return void
   */
  public function testFromCallable(){
    $request = ServerRequestFactory::fromGlobals();
    $response = (new ResponseFactory)->createResponse();
    $errorResponse =(new ResponseFactory)->createResponse(500);

    $middlewareFactory = new MiddlewareFactory();
    $middleware = $middlewareFactory->fromCallable(fn() => $response);
    $returnedResponse = $middleware->process($request, new RequestHandler($errorResponse));

    $this->assertEquals($response, $returnedResponse);
    $this->assertTrue($returnedResponse->getStatusCode() !== 500);
  }

  /**
   * This test makes sure the passed response will be returned when processing the middleware.
   *
   * @return void
   */
  public function testFromResponse(){
    $request = ServerRequestFactory::fromGlobals();
    $response = (new ResponseFactory)->createResponse();
    $errorResponse =(new ResponseFactory)->createResponse(500);

    $middlewareFactory = new MiddlewareFactory();
    $middleware = $middlewareFactory->fromResponse($response);
    $returnedResponse = $middleware->process($request, new RequestHandler($errorResponse));

    $this->assertEquals($response, $returnedResponse);
    $this->assertTrue($returnedResponse->getStatusCode() !== 500);
  }

  /**
   * This test makes sure the passed response-factory will be used to create the response when processing the middleware.
   *
   * @return void
   */
  public function testFromResponseFactory(){
    $request = ServerRequestFactory::fromGlobals();
    $responseFactory = new ResponseFactory;
    $errorResponse = $responseFactory->createResponse(500);

    $middlewareFactory = new MiddlewareFactory();
    $middleware = $middlewareFactory->fromResponseFactory($responseFactory);
    $returnedResponse = $middleware->process($request, new RequestHandler($errorResponse));

    $this->assertInstanceOf(ResponseInterface::class, $returnedResponse);
    $this->assertTrue($returnedResponse->getStatusCode() !== 500);
  }

}