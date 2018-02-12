<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
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
     * @var mixed[]
     */
    private $routes = [];

    /**
     * @var string[]
     */
    private $paths  = [];

    /**
     * ルートを追加する
     *
     * 同じパスを使用するルートが定義された場合、
     * 前に定義されたルートは上書きされる。
     *
     * @param   string  $name
     * @param   string  $path
     * @param   string[]    $methods    [optional]
     *      許容するHTTPメソッドを持つ配列。
     *      nullを指定した場合はすべてのメソッドを許容する。
     * @param   mixed[] $data   [optional]
     *      ルートに一致した場合に返される値。
     *
     * @return  void
     *
     * @throws  \LogicException
     */
    public function addRoute(
        string $name,
        string $path,
        array $methods = null,
        array $data = []
    ){
        if(isset($this->routes[$name])){
            throw new \LogicException;
        }

        $path       = substr($path, 0, 1) !== "/" ? "/{$path}" : $path;
        $methods    = $methods === null ? null : array_unique(
            array_map("strtoupper",
                array_filter(
                    $methods,
                    function($v){
                        return is_string($v) && $v !== "";
                    }
                )
            )
        );

        if(isset($this->paths[$path])){
            unset($this->routes[$this->paths[$path]]);
        }

        $this->paths[$path]     = $name;
        $this->routes[$name]    = [
            "path"  => $path,
            "allow" => ($methods === null || empty($methods)) ? null : $methods,
            "data"  => $data
        ];
    }

    /**
     * GETメソッドを許容するルートを追加する
     *
     * @param   string  $name
     * @param   string  $path
     * @param   mixed[] $data   [optional]
     *      ルートに一致した場合に返される値。
     *
     * @return  void
     */
    public function get(string $name, string $path, array $data = []){
        $this->addRoute($name, $path, ["GET"], $data);
    }

    /**
     * POSTメソッドを許容するルートを追加する
     *
     * @param   string  $name
     * @param   string  $path
     * @param   mixed[] $data   [optional]
     *      ルートに一致した場合に返される値。
     *
     * @return  void
     */
    public function post(string $name, string $path, array $data = []){
        $this->addRoute($name, $path, ["POST"], $data);
    }

    /**
     * PUTメソッドを許容するルートを追加する
     *
     * @param   string  $name
     * @param   string  $path
     * @param   mixed[] $data   [optional]
     *      ルートに一致した場合に返される値。
     *
     * @return  void
     */
    public function put(string $name, string $path, array $data = []){
        $this->addRoute($name, $path, ["PUT"], $data);
    }

    /**
     * PATCHメソッドを許容するルートを追加する
     *
     * @param   string  $name
     * @param   string  $path
     * @param   mixed[] $data   [optional]
     *      ルートに一致した場合に返される値。
     *
     * @return  void
     */
    public function patch(string $name, string $path, array $data = []){
        $this->addRoute($name, $path, ["PATCH"], $data);
    }

    /**
     * DELETEメソッドを許容するルートを追加する
     *
     * @param   string  $name
     * @param   string  $path
     * @param   mixed[] $data   [optional]
     *      ルートに一致した場合に返される値。
     *
     * @return  void
     */
    public function delete(string $name, string $path, array $data = []){
        $this->addRoute($name, $path, ["DELETE"], $data);
    }

    /**
     * ルーターを返す
     *
     * @param string $method
     */
    public function createRouter(string $method){

    }

    /**
     * リバースルーターを返す
     */
    public function createReverseRouter(){

    }
}