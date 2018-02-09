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
class Dispatcher{

    const FOUND                 = 0;
    const NOT_FOUND             = 1;
    const METHOD_NOT_ALLOWED    = 2;

    /**
     * @var ShortRegexInterface[]
     */
    private $sregex = [];
    
    /**
     * @var RouteCollector
     */
    private $collector;
    
    /**
     * ShortRegexを追加する
     * 
     * @param   string  $modifier
     * @param   ShortRegexInterface $sregex
     */
    public function addShortRegex(string $modifier, ShortRegexInterface $sregex){
        $this->sregex[$modifier]    = $sregex;
    }
    
    /**
     * ShortRegexを削除する
     * 
     * @param   string  $modifier
     */
    public function removeShortRegex(string $modifier){
        if(isset($this->sregex[$modifier])){
            unset($this->sregex[$modifier]);
        }
    }

    /**
     * Constructor
     *
     * @param   RouteCollector  $collector
     */
    public function __construct(RouteCollector $collector){
        $this->collector    = $collector;
    }
    
    /**
     * ルーティングを行いその結果を返す
     * 
     * @param   string  $method
     *      HTTPメソッド。
     * @param   string  $path
     *      リクエストパス。クエリ部分は無視される。
     */
    public function dispatch(string $method, string $path){
        
    }
    
    /**
     * 指定したルート名とパラメーターからパス(およびクエリ)を生成する
     * 
     * @param   string  $name
     *      ルート名
     * @param   string[]    $params
     *      パラメーターリスト。値は文字列として扱うことができなければならない。
     * @param   bool
     *      使用しなかったパラメーターをクエリとして末尾に追加するか。
     * 
     * @return  string
     */
    public function reverseRoute(string $name, array $params, bool $addQuery = true){
        
    }
}