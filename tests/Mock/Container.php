<?php declare(strict_types=1);

namespace ApiKit\Middleware\Tests\Mock;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {

  /**
   * @param array $config
   */
  public function __construct(array $config){
    $this->config = $config;
  }

  /**
   * @param string $id
   * @return void
   */
  public function get($id){
    return $this->config[$id]();
  }

  /**
   * @param string $id
   * @return void
   */
  public function has($id){
    return isset($this->config[$id]);
  }

}