<?php declare(strict_types=1);

namespace ApiKit\Middleware\Tests\Unit;

use ApiKit\Middleware\MiddlewareContainer;
use ApiKit\Middleware\Tests\Mock\{ Container, Middleware, RequestHandler};
use Laminas\Diactoros\{ ServerRequestFactory, ResponseFactory };

final class MiddlewareContainerTest extends \PHPUnit\Framework\TestCase {

  /**
   * This test makes sure that the MiddlewareInterface-instance is fetched from the container
   * and process() is called. If it works the response will be passed through the layers:
   * 
   *  LazyMiddleware -> Middleware -> RequestHandler => Response
   *
   * @return void
   */
  public function test(){
    $container = new Container([
      Middleware::class => fn() => new Middleware()
    ]);

    $request = ServerRequestFactory::fromGlobals();
    $response = (new ResponseFactory)->createResponse();
    $next = new RequestHandler($response);

    $lazyMiddleware = new MiddlewareContainer($container, Middleware::class);
    $returnedResponse = $lazyMiddleware->process($request, $next);

    $this->assertEquals($response, $returnedResponse);
  }

}