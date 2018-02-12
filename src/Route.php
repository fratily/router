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
class Route{

    private $methods;

    private $path;

    private $data;

    /**
     *
     * @param string $path
     * @param array $methods
     * @param array $data
     */
    public function __construct(string $path, array $methods = null, array $data = []){
        $this->setPath($path);
        $this->setMethods($methods);
        $this->setData($data);
    }

    /**
     * 許容するHTTPメソッド一覧を返す
     *
     * nullの場合はすべてのHTTPメソッドを許容する。
     *
     * @return  string[]|null
     */
    public function getMethods(){
        return $this->methods;
    }

    /**
     * 一致パスを返す
     *
     * @return  string
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * 一致した場合に取得するデータを返す
     *
     * @return  mixed[]
     */
    public function getData(){
        return $this->data;
    }

    /**
     * 許容するメソッドを設定する
     *
     * @param   string|string[]|null
     *
     * @return  $this
     */
    public function setMethods(array $methods = null){
        if($methods === null){
            $this->methods  = null;
        }else if(is_string($methods) || is_array($methods)){
            $methods    = array_filter(
                (array)$methods,
                function($v){
                    return is_string($v) && $v !== "";
                }
            );

            if(!empty($methods)){
                $this->methods  = [];

                foreach($methods as $method){
                    $this->methods[strtoupper($method)] = true;
                }
            }else{
                $this->methods  = null;
            }
        }else{
            throw new \InvalidArgumentException();
        }

        return $this;
    }

    /**
     * 一致パスを設定する
     *
     * @param   string  $path
     *
     * @return  $this
     */
    public function setPath(string $path){
        $this->path = substr($path, 0, 1) !== "/" ? "/{$path}" : $path;

        return $this;
    }

    /**
     * 一致した場合に返すデータを設定する
     *
     * @param   mixed[] $data
     *
     * @return  $this
     */
    public function setData(array $data){
        $this->data = $data;

        return $this;
    }
}