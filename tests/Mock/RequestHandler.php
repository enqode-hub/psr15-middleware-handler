<?php declare(strict_types=1);

namespace ApiKit\Middleware\Tests\Mock;

use Psr\Http\Message\{ ResponseInterface, ServerRequestInterface };
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface {

  /**
   * @var ResponseInterface
   */
  private $response;

  /**
   * @param ResponseInterface $response
   */
  public function __construct(ResponseInterface $response){
    $this->response = $response;
  }

  /**
   * @param ServerRequestInterface $request
   * @param RequestHandlerInterface $next
   * @return ResponseInterface
   */
  public function handle(ServerRequestInterface $request): ResponseInterface {
    return $this->response;
  }

}