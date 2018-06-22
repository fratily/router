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
namespace Fratily\Router\Parser;

/**
 *
 */
class Segment{

    const T_PLAIN   = "plain";
    const T_REGEX   = "regex";

    private static $matchers    = [
        self::T_PLAIN   => [self::class, "_matchPlain"],
        self::T_REGEX   => [self::class, "_matchRegex"],
    ];

    /**
     * @var callable[]
     */
    private static $convertors      = [];

    /**
     * @var callable[]
     */
    private static $reconvertors    = [
        self::T_PLAIN   => [self::class, "_reconvertPlain"],
        self::T_REGEX   => [self::class, "_reconvertRegex"],
    ];

    /**
     * @var mixed
     */
    private $type;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var bool[]
     */
    private $cache  = [];

    /**
     * タイプを追加する
     *
     * @param   string|integer  $type
     * @param   callable    $matcher
     *
     * @return  void
     *
     * @throws  \InvalidArgumentException
     */
    public static function addType(
        $type,
        callable $matcher,
        callable $convertor = null,
        callable $reconvertor = null
    ){
        if(!is_string($type) && !is_int($type)){
            throw new \InvalidArgumentException();
        }

        if(array_key_exists($type, self::$matchers)){
            throw new \InvalidArgumentException();
        }

        self::$matchers[$type] = $matcher;

        if($convertor !== null){
            self::$convertors[$type]    = $convertor;
        }

        if($reconvertor !== null){
            self::$reconvertors[$type]  = $reconvertor;
        }
    }

    /**
     * Constructor
     *
     * @param   mixed   $type
     * @param   string  $rule
     *
     * @throws  \InvalidArgmentException
     */
    public function __construct($type, string $rule){
        $this->type = $type;
        $this->rule = $rule;
    }

    /**
     * 指定文字列がこのセグメントに一致するか確認する
     *
     * @param   string  $segment
     *
     * @return  bool
     */
    public function isMatch(string $segment){
        if(!array_key_exists($segment, $this->cache)){
            $this->cache[$segment]  = self::$matchers[$this->type](
                $this->rule,
                $segment
            );
        }

        return $this->cache[$segment];
    }

    /**
     * セグメント文字列を使いやすい値に変換する
     *
     * @param   string  $segment
     *
     * @return  mixed
     */
    public function convert(string $segment){
        if(array_key_exists($this->type, self::$convertors)){
            $callback   = self::$convertors[$this->type];

            return $callback($segment);
        }

        return $segment;
    }

    /**
     * 値をセグメント文字列に変換する
     *
     * @param   mixed   $value
     *
     * @return  string
     */
    public function reconvert($value){
        if(array_key_exists($this->type, self::$reconvertors)){
            $callback   = self::$reconvertors[$this->type];

            $value  = $callback($this->rule, $value);
        }

        if(is_scalar($value) || (is_object($value) && method_exists($value, "__toString"))){
            return (string)$value;
        }

        throw new \LogicException;
    }

    public function getType(){
        return $this->type;
    }

    public function getRule(){
        return $this->rule;
    }

    /**
     * 名前を取得する
     *
     * @return  string|null
     */
    public function getName(){
        return $this->name;
    }

    /**
     * 名前を設定する
     *
     * @param   string  $name
     *
     * @return  static
     */
    public function withName(string $name = null){
        if($this->name === $name){
            return $this;
        }

        $clone          = clone $this;
        $clone->name    = $name;

        return $clone;
    }

    public static function _matchPlain(string $rule, string $segment){
        return $rule === $segment;
    }

    public static function _matchRegex(string $rule, string $segment){
        return (bool)preg_match("/\A{$rule}\z/", $segment);
    }

    public static function _reconvertPlain(string $rule, $value){
        return $rule;
    }

    public static function _reconvertRegex(string $rule, $value){
        if(!is_scalar($value) && !(is_object($value) && method_exists($value, "__toString"))){
            throw new \LogicException;
        }

        $value  = (string)$value;

        if(!(bool)preg_match("/\A{$rule}\z/", $value)){
            throw new \LogicException;
        }

        return $value;
    }
}