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
    const METHOD_NOT_ALLOWD = 3;

    const RAW   = 1;
    const REG   = 2;
    const SREG  = 3;

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
        if($type !== self::RAW && $type !== self::REG && $type !== self::SREG){
            throw new \InvalidArgumentException();
        }

        $id = substr(hash("md5", $type . $match, false), 0, 16);

        if(!isset(self::$rule[$id])){
            self::$rule[$id]    = [
                "type"      => $type,
                "match"     => $match,
                "result"    => []
            ];

            if($type === self::RAW){
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

            if($type === self::SREG){
                if(isset(self::$sregex[$match])){
                    $class  = self::$sregex[$match];
                    $result = $class::match($segment);
                }
            }else if($type === self::REG){
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
            if($rule["type"] === self::SREG && $rule["match"] === $name){
                $rule["result"] = [];
            }
        }
    }

    public function addRoute(string $path, array $data){
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
}