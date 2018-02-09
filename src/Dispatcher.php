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
     * @var bool[]
     */
    private $ruleCache  = [];
    
    /**
     * ShortRegexを追加する
     * 
     * @param   string  $modifier
     * @param   ShortRegexInterface $sregex
     */
    public function addShortRegex(string $modifier, ShortRegexInterface $sregex){
        if(isset($this->sregex[$modifier])){
            throw new \InvalidArgumentException();
        }
        
        $this->sregex[$modifier]    = $sregex;
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
    
    /**
     * 文字列がルールに一致するか確認する
     * 
     * @param   string  $id
     * @param   string  $target
     * 
     * @return  bool
     */
    protected function matchedRule(string $id, string $target){
        $cacheKey   = "{$id}:{$target}";
        
        if(!isset($this->ruleCache[$cacheKey])){
            $result = false;
            
            if(($rule = $this->collector->getRule($id)) !== null){
                if($rule["type"] === RouteCollector::RAW){
                    $result = $target === $rule["match"];
                }else if($rule["type"] === RouteCollector::REG){
                    $result = (bool)preg_match("/\A{$rule["match"]}\z/", $target);
                }else if($rule["type"] === RouteCollector::SREG){
                    if(isset($this->sregex[$rule["match"]])){
                        $result = $this->sregex[$rule["match"]]->match($target);
                    }
                }
            }
            
            $this->ruleCache[$cacheKey] = $result;
        }
        
        return $this->ruleCache[$cacheKey];
    }
}