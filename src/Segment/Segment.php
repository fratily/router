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
namespace Fratily\Router\Segment;

/**
 *
 */
class Segment{

    const MODE_SAME = 1;
    const MODE_TYPE = 2;

    /**
     * @var string[]
     */
    private static $types   = [
        "bool"  => Type\BoolType::class,
        "int"   => Type\IntType::class,
        "octal" => Type\OctalIntType::class,
        "hex"   => Type\HexIntType::class,
        "float" => Type\FloatType::class,
        "any"   => Type\AnyType::class,
    ];

    /**
     * @var int
     */
    private $mode;

    /**
     * @var string
     */
    private $modeData;

    /**
     * @var string|null
     */
    private $name;

    /**
     * セグメント一致タイプを追加する
     *
     * @param   string  $name
     *  タイプ名
     * @param   string  $class
     *  クラス名
     *
     * @return  void
     *
     * @throws  \InvalidArgumentException
     */
    public static function addType(string $name, string $class){
        if(!class_exists($class)){
            throw new \InvalidArgumentException();
        }

        if(!in_array(Type\TypeInterface::class, class_implements($class))){
            throw new \InvalidArgumentException();
        }

        self::$types[$name] = $class;
    }

    /**
     *
     *
     * @param   string  $type
     *
     * @return  static
     */
    public static function createTypeMode(string $type, string $name){
        static $cache   = [];

        if(!array_key_exists($type, $cache)){
            $cache[$type]   = new static(self::MODE_TYPE, $type);
        }

        return $cache[$type]->withName($name);
    }

    /**
     *
     *
     * @param   string  $same
     *
     * @return  static
     */
    public static function createSameMode(string $same){
        static $cache   = [];

        if(!array_key_exists($same, $cache)){
            $cache[$same]   = new static(self::MODE_SAME, $same);
        }

        return $cache[$same];
    }

    /**
     * Constructor
     *
     * @param   int $mode
     * @param   string  $val
     *
     * @throws  \InvalidArgumentException
     */
    protected function __construct(int $mode, string $val){
        if($mode !== self::MODE_TYPE && $mode !== self::MODE_SAME){
            throw new \InvalidArgumentException();
        }

        $this->mode     = $mode;
        $this->modeData = $val;
    }

    /**
     * 指定文字列がこのセグメントに一致するか確認する
     *
     * @param   string  $segment
     *
     * @return  bool
     */
    public function isMatch(string $segment){
        if($this->mode === self::MODE_TYPE){
            return $this->getValueWithType($segment) !== null;
        }else if($this->mode === self::MODE_SAME){
            return $this->getValueWithSame($segment) !== null;
        }

        return false;
    }

    /**
     * 値を取得する
     *
     * @param   string  $segment
     *
     * @return  mixed|null
     */
    public function getValue(string $segment){
        if($this->mode === self::MODE_TYPE){
            return $this->getValueWithType($segment);
        }else if($this->mode === self::MODE_SAME){
            return $this->getValueWithSame($segment);
        }

        return null;
    }

    private function getValueWithType(string $segment){
        static $cache   = [];

        if(!array_key_exists($this->modeData, self::$types)){
            return null;
        }

        if(!array_key_exists(self::$types[$this->modeData], $cache)){
            $cache[self::$types[$this->modeData]]   = [];
        }

        if(!array_key_exists($segment, $cache[self::$types[$this->modeData]])){
            $class  = self::$types[$this->modeData];

            $cache[self::$types[$this->modeData]][$segment] = $class::valid($segment);
        }

        return $cache[self::$types[$this->modeData]][$segment];
    }

    private function getValueWithSame(string $segment){
        return $segment === $this->getModeData() ? $segment : null;
    }

    public function getMode(){
        return $this->mode;
    }

    public function getModeData(){
        return $this->modeData;
    }

    /**
     * 名前を取得する
     *
     * @return  string|null
     */
    public function getName(){
        return $this->name;
    }

    public function withName(string $name = null){
        if($this->name === $name){
            return $this;
        }

        $clone  = clone $this;

        $clone->name    = $name;

        return $clone;
    }
}