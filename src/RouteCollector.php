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

    const RAW   = 1;
    const REG   = 2;
    const SREG  = 3;
    
    const REG_SEG   = "/\A([A-Z_][0-9A-Z_]*)(:|\|)(.+?)\z/i";
    
    /**
     * @var mixed[][]
     */
    private $rule   = [];
    
    /**
     * @var mixed[][]
     */
    private $node   = [];
    
    /**
     * ルールを取得する
     * 
     * @param   string  $id
     * 
     * @return  mixed[]|null
     */
    public function getRule(string $id){
        return $this->rule[$id] ?? null;
    }
    
    /**
     * ルールの存在を確認する
     * 
     * @param   string  $id
     */
    public function hasRule(string $id){
        return isset($this->rule[$id]);
    }
    
    /**
     * ルールを追加する
     * 
     * @param   int $type
     * @param   string  $match
     * 
     * @return  string
     *      ルールIDが返される。
     * 
     * @throws  \InvalidArgumentException
     */
    protected function addRule(int $type, string $match){
        if($type !== self::RAW && $type !== self::REG && $type !== self::SREG){
            throw new \InvalidArgumentException();
        }
        
        //  ここの正規表現はパフォーマンスやその他もろもろとの兼ね合いで調整
        if($type === self::REG && (bool)preg_match("/\A[0-9A-Z-_]\z/i", $match)){
            $type   = self::RAW;
        }
        
        $id = hash("md5", $type . $match);
        
        if(!isset($this->rule[$id])){
            $this->rule[$id]    = [
                "type"  => $type,
                "match" => $match
            ];
        }
        
        return $this->rule[$id];
    }
    
    /**
     * ノードを取得する
     * 
     * @param   string  $id
     * 
     * @return  mixed[]
     */
    public function getNode(string $id){
        return $this->node[$id] ?? null;
    }
    
    /**
     * ノードの存在を確認する
     * 
     * @param   string  $id
     * 
     * @return  bool
     */
    public function hasNode(string $id){
        return isset($this->node);
    }
    
    /**
     * ノードを追加する
     * 
     * @param   int $row
     *      木構造での階層
     * @param   string  $ruleId
     * @param   string  $parentId
     * @param   string  $method
     * @param   string  $name
     * 
     * @return  string
     * 
     * @throws  \InvalidArgumentException
     */
    protected function addNode(
        int $row,
        string $ruleId,
        string $parentId,
        string $method,
        string $name = null
    ){
        if(!$this->hasRule($ruleId)){
            throw new \InvalidArgumentException();
        }else if(!isset($this->node[$parentId])){
            throw new \InvalidArgumentException();
        }
        
        $nodeId = hash("md5", $parentId . $row . $ruleId . $method . ($name ?? ""));
        
        if(!isset($this->node[$nodeId])){
            $this->node[$nodeId]    = [
                "rule"  => $ruleId,
                "name"  => $name,
                "data"  => null,
                "child" => []
            ];
            
            if(!in_array($nodeId, $this->node[$parentId]["child"])){
                $this->node[$parentId]["child"][]   = $nodeId;
            }
        }
        
        return $nodeId;
    }
}