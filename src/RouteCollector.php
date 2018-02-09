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
}