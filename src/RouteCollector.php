<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento-oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Router;

/**
 *
 */
class RouteCollector{

    /**
     * @var Router[]
     */
    private $router = [];

    /**
     * @var ReverseRouter[]
     */
    private $reverseRouter  = [];

    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * @var bool
     */
    private $lock   = false;

    /**
     * ルートインスタンスを取得する
     *
     * @param   string  $name
     *
     * @return  Route|null
     */
    public function get(string $name){
        return $this->routes[$name] ?? null;
    }

    /**
     * ルートインスタンスを追加する
     *
     * @param   Route   $route
     * @param   string  $name
     *
     * @return  $this
     *
     * @throws  \InvalidArgumentException
     */
    public function add(Route $route){
        if($this->lock){
            throw new \LogicException;
        }

        if($route->getName() === null){
            do{
                $name   = md5($route->getPath() . bin2hex(random_bytes(4)));
            }while(array_key_exists($name, $this->routes));

            $route  = $route->withName($name);
        }

        if(array_key_exists($route->getName(), $this->routes)){
            throw new \InvalidArgumentException();
        }

        $this->routes[$route->getName()]    = $route;

        return $this;
    }

    /**
     * ルーターを返す
     *
     * @param   string  $host
     * @param   string  $method
     *
     * @return  Router
     */
    public function router(string $host, string $method){
        $this->lock = true;
        $key        = "{$host}:{$method}";

        if(!isset($this->router[$key])){
            $this->router[$key]  = new Router(
                array_filter($this->routes, function($route) use ($host, $method){
                    return $route->isEnable($host, $method);
                })
            );
        }

        return $this->router[$key];
    }

    /**
     * リバースルーターを返す
     *
     * @param   string  $name
     *
     * @return  ReverseRouter
     */
    public function reverseRouter(string $name){
        $this->lock = true;

        if(!isset($this->reverseRouter[$name])){
            $route  = $this->get($name);

            $this->reverseRouter[$name] = $route === null
                ? null
                : new ReverseRouter($route)
            ;
        }

        return $this->reverseRouter[$name];
    }
}
