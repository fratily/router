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
class Router{

    const FOUND             = 1;
    const NOT_FOUND         = 2;

    private static $rule    = [];

    private static $sregex  = [];

    private $tree   = [];

    /**
     * 指定したIDのルールを返す
     *
     * @param   string  $id
     *
     * @return  mixed[]|null
     */
    protected static function getRule(string $id){
        return self::$rule[$id] ?? null;
    }

    /**
     * 指定したIDのルールタイプを返す
     *
     * @param   string  $id
     *
     * @return  int|null
     */
    protected static function getRuleType(string $id){
        return self::$rule[$id]["type"] ?? null;
    }

    /**
     * 指定したIDのルールの一致確認文字列を返す
     *
     * @param   string  $id
     *
     * @return  string|null
     */
    protected static function getRuleMatch(string $id){
        return self::$rule[$id]["match"] ?? null;
    }

    /**
     * 指定したIDのルールが存在するか確認する
     *
     * @param   string  $id
     *
     * @return  bool
     */
    protected static function hasRule(string $id){
        return isset(self::$rule[$id]);
    }

    /**
     * ルールを追加してIDを返す
     *
     * @param   int $type
     * @param   string  $match
     *
     * @return  string
     *
     * @throws  \InvalidArgumentException
     */
    protected static function addRule(int $type, string $match){
        if($type !== Parser::RAW && $type !== Parser::REG && $type !== Parser::SREG){
            throw new \InvalidArgumentException();
        }

        $id = substr(hash("md5", $type . $match, false), 0, 16);

        if(!isset(self::$rule[$id])){
            self::$rule[$id]    = [
                "type"      => $type,
                "match"     => $match,
                "result"    => []
            ];

            if($type === Parser::RAW){
                self::$rule[$id]["result"][$match]  = true;
            }
        }

        return $id;
    }

    /**
     * 文字列が指定したIDのルールに一致するか確認する
     *
     * @param   string  $id
     * @param   string  $segment
     *
     * @return  bool
     */
    protected static function matchRule(string $id, string $segment){
        if(!isset(self::$rule[$id])){
            return false;
        }

        if(!isset(self::$rule[$id]["result"][$segment])){
            $type   = self::$rule[$id]["type"];
            $match  = self::$rule[$id]["match"];
            $result = false;

            if($type === Parser::SREG){
                if(isset(self::$sregex[$match])){
                    $class  = self::$sregex[$match];
                    $result = $class::match($segment);
                }
            }else if($type === Parser::REG){
                $result = (bool)preg_match("/\A{$match}\z/", $segment);
            }else{
                $result = $match === $segment;
            }

            self::$rule[$id]["result"][$segment]    = $result;
        }

        return self::$rule[$id]["result"][$segment];
    }

    /**
     * 指定した名前のShortRegexクラス名を返す
     *
     * @param   string  $name
     *
     * @return  string|null
     */
    public static function getShortRegex(string $name){
        return self::$sregex[$name] ?? null;
    }

    /**
     * 指定した名前のShortRegexクラスがあるか確認する
     *
     * @param   string  $name
     *
     * @return  bool
     */
    public static function hasShortRegex(string $name){
        return isset(self::$sregex[$name]);
    }

    /**
     * ShortRegexクラス名を登録する
     *
     * @param   string  $name
     * @param   string  $class
     *
     * @return  void
     *
     * @throws  \InvalidArgumentException
     */
    public static function addShortRegex(string $name, string $class){
        if($name === ""){
            throw new \InvalidArgumentException();
        }else if(!class_exists($class)){
            throw new \InvalidArgumentException();
        }

        $ref    = new \ReflectionClass($class);

        if(!$ref->implementsInterface(ShortRegexInterface::class)){
            throw new \InvalidArgumentException();
        }

        self::$sregex[$name]    = $ref->getName();

        foreach(self::$rule as &$rule){
            if($rule["type"] === Parser::SREG && $rule["match"] === $name){
                $rule["result"] = [];
            }
        }
    }

    /**
     * Constructor
     *
     * @param   array[] $routes
     */
    public function __construct(array $routes){
        foreach($routes as $route){
            $this->addRoute($route[0], $route[1]);
        }
    }

    /**
     * ルートを追加する
     *
     * @param   string  $path
     * @param   mixed[] $data
     *
     * @return  void
     */
    protected function addRoute(string $path, array $data){
        $segments   = Parser::split2segments($path);
        $nodes      = &$this->tree;

        foreach($segments as $segment){
            $segment    = Parser::segment($segment);
            $rule       = self::addRule($segment["type"], $segment["match"]);

            if(!isset($nodes[$rule])){
                $nodes[$rule]   = [
                    "rule"  => $rule,
                    "param" => $segment["param"],
                    "child" => [],
                    "data"  => null
                ];
            }

            $parent = &$nodes[$rule];
            $nodes  = &$nodes[$rule]["child"];
        }

        $parent["data"] = $data;
    }

    /**
     * 一致するルートを探す
     *
     * @param   string  $path
     *
     * @return  mixed[]
     */
    public function search(string $path){
        $result = [self::NOT_FOUND, [], []];
        $search = $this->searchNode(
            array_reverse(Parser::split2segments(explode("?", $path, 2)[0])),
            $this->tree
        );

        if($search !== false){
            $result = [self::FOUND, $search[0], $search[1]];
        }

        return $result;
    }

    /**
     * ルーティングツリーのノードを探索する
     *
     * @param   string[]    $segments
     *      セグメントごとに格納されたスタック
     * @param   mixed[] $node
     *      ノード
     *
     * @return  mixed[]|bool
     */
    private function searchNode(array $segments, array $nodes, array $params = []){
        $segment    = array_pop($segments);

        foreach($nodes as $rule => $node){
            if(self::matchRule($rule, $segment)){
                if(isset($node["param"])){   //  セグメントをパラメーターに追加
                    if(self::getRuleType($rule) === Parser::SREG){
                        $class  = self::getShortRegex(self::getRuleMatch($rule));
                        $param  = $class::convert($segment);
                    }else{
                        $param  = $segment;
                    }

                    $params[$node["param"]] = $param;
                }

                if(empty($segments)){
                    if(isset($node["data"])){
                        return [$params, $node["data"]];
                    }

                    continue;
                }

                $return = $this->searchNode($segments, $node["child"], $params);

                if($return !== false){
                    return $return;
                }
            }
        }

        return false;
    }
}