<?php declare(strict_types=1);

namespace ApiKit\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{ ResponseInterface, ServerRequestInterface };
use Psr\Http\Server\{ MiddlewareInterface, RequestHandlerInterface };

class MiddlewareContainer implements MiddlewareInterface {

  /**
   * @var ContainerInterface
   */
  private $container;

  /**
   * @var string
   */
  private $class;

  /**
   * @param ContainerInterface $container
   * @param string $class
   */
  public function __construct(ContainerInterface $container, string $class) {
    $this->container = $container;
    $this->class = $class;
  }

  /**
   * @param ServerRequestInterface $request
   * @param RequestHandlerInterface $next
   * @return ResponseInterface
   */
  public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface {
    $middleware = $this->container->get($this->class);
    return $middleware->process($request, $next);
  }

}