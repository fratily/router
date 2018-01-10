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

    const RAW       = 1;
    const REGEX     = 2;
    const SREGEX    = 3;
    
    const REGEX_SEG = "/\A((?<name>[A-Za-z_][0-9A-Za-z_]*)?(?<type>:|\|))?(?<regex>.+?)\z/x";
    
    /**
     * @var string
     */
    private $groupPrefix    = "";
    
    /**
     * @var mixed[]
     */
    private $groupParams    = [];
    
    /**
     */
    private $static = [];
    
    /**
     */
    private $tree   = [];
    
    /**
     * ルート定義をグループ化する
     *
     * @param   string|mixed[]  $common
     *      stringならばurlの先頭に指定文字列を追加し、arrayならパラメーターの
     *      共通値を設定する。arrayの場合の共通パラメーターは最も優先度が低く、
     *      addRoute()で定義されるパラメーターに上書きされる可能性がある。
     * @param   callable    $callback
     *      このコールバック関数が実行される間だけグループ化が有効となる。
     *      コールバックは第一引数にこのオブジェクトが渡される。
     */
    public function addGroup($common, callable $callback){
        if(is_string($common)){
            $prev   = $this->groupPrefix;
            $this->groupPrefix  = $prev . $common;
        }else if(is_array($common)){
            $prev   = $this->groupParams;
            $this->groupParams  = $common + $prev;
        }else{
            throw new \InvalidArgumentException;
        }
        
        $callback($this);
        
        if(is_string($common)){
            $this->groupPrefix  = $prev;
        }else{
            $this->groupParams  = $prev;
        }
    }

    /**
     * ルートを定義する
     *
     * @param   string|string[] $methods
     *      一致するメソッド、もしくはそのリスト。
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function addRoute($methods, string $url, array $params = []){
        $methods    = array_unique(
            array_map(
                "strtoupper",
                array_filter(
                    (array)$methods,
                    "is_string"
                )
            )
        );
        $url    = $this->groupPrefix . $url;
        $url    = substr($url, 0, 1) === "/" ? $url : "/{$url}";
        $params = $params + $this->groupParams;
        $nodes  = $this->createNodes(Parser::split2segments($url));
        
        if(is_string($nodes)){
            foreach($methods as $method){
                $this->static[$method][$url]    = $params;
            }
        }else if(is_array($nodes)){
            $parents    = [];
            $first      = true;
            
            foreach($methods as $method){
                if(!isset($this->tree[$method])){
                    $this->tree[$method]    = [];
                }
                
                $parents[$method] = &$this->tree[$method];
            }
            
            foreach($nodes as $name => $node){
                if($first){
                    foreach($parents as $key => &$parent){
                        if(!isset($parent[$name])){
                            $parent[$name]  = $node;
                        }
                        
                        $parents[$key]  = &$parent[$name];
                    }
                    
                    $first  = false;
                }else{
                    foreach($parents as $key => &$parent){
                        if(!isset($parent["children"][$name])){
                            $parent["children"][$name]  = $node;
                        }
                        
                        $parents[$key]  = &$parent["children"][$name];
                    }
                }
            }
            
            foreach($parents as &$parent){
                $parent["end"]  = array_merge($parent["end"] ?? [], $params);
            }
        }
    }

    /**
     * addRoute("GET",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function get(string $url, array $params = []){
        $this->addRoute("GET", $url, $params);
    }

    /**
     * addRoute("POST",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function post(string $url, array $params = []){
        $this->addRoute("POST", $url, $params);
    }

    /**
     * addRoute("PUT",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function put(string $url, array $params = []){
        $this->addRoute("PUT", $url, $params);
    }

    /**
     * addRoute("PATCH",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function patch(string $url, array $params = []){
        $this->addRoute("PATCH", $url, $params);
    }

    /**
     * addRoute("DELETE",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function delete(string $url, array $params = []){
        $this->addRoute("DELETE", $url, $params);
    }

    /**
     * addRoute("HEAD",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function head(string $url, array $params = []){
        $this->addRoute("HAED", $url, $params);
    }

    /**
     * 正規表現などを使用しないルールのリストを返す
     *
     * @return  mixed
     */
    public function getStatic(){
        return $this->static;
    }

    /**
     * ルーティングルールの木構造データを返す
     *
     * @return  mixed
     */
    public function getTree(){
        return $this->tree;
    }
    
    /**
     * セグメントごとのノード構造を作成する
     * 
     * @param   string[]    $segments
     * 
     * @return  string|array[]|bool
     *      文字列が返された場合は文字列比較で一致確認できるルール。
     *      配列にはノード構造化されたセグメントが上層から順番に格納されている。
     */
    private function createNodes(array $segments){
        $static = true;
        $nodes  = [];
        
        foreach($segments as $segment){
            if(substr($segment, 0, 1) === "{" && substr($segment, -1, 1) === "}"){
                $static     = false;
                $segment    = 2 < strlen($segment)
                    ? substr($segment, 1, strlen($segment) - 2)
                    : "";
                
                if(!(bool)preg_match(self::REGEX_SEG, $segment, $m)){
                    return false;
                }
                
                $name   = ($m["name"] ?? "") === "" ? null : $m["name"];
                $type   = ($m["type"] ?? ":") === ":" ? self::REGEX : self::SREGEX;
                $rule   = $m["regex"];
            }else{
                $name   = null;
                $type   = self::RAW;
                $rule   = $segment;
            }
            
            if($type === self::SREGEX
                && !(bool)preg_match("/\A[A-Za-z_][0-9A-Za-z_]*\z/", $rule)
            ){
                return false;
            }

            $key            = hash("md5", $type . $rule . ($name ?? ""));
            $nodes[$key]    = [
                "type"  => $type,
                "rule"  => $rule,
                "name"  => $name,
                "children"  => []
            ];
        }
        
        if($static){
            return "/" . implode("/", $segments);
        }else{
            return $nodes;
        }
    }
}