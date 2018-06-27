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
namespace Fratily\Router;

/**
 *
 */
class Route{

    const METHODS   = [
        "GET"       => true,
        "POST"      => true,
        "PUT"       => true,
        "DELETE"    => true,
    ];

    /**
     * @var string
     */
    private $path;

    /**
     * @var string|null
     */
    private $host;

    /**
     * @var string[]
     */
    private $allows;

    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @var string|name
     */
    private $name;

    /**
     * インスタンスを生成する
     *
     * @param   string  $path
     * @param   string  $host
     * @param   string[]    $allows
     * @param   mixed[] $data
     *
     * @return  static
     */
    public static function newInstance(
        string $path,
        string $host = "*",
        $allows = "GET",
        array $data = []
    ){
        return new static($path, $host, $allows, $data);
    }

    /**
     * パス文字列の正規化
     *
     * @param   string  $path
     *
     * @return  string
     */
    public static function normalizePath(string $path){
        return substr($path, 0, 1) === "/" ? substr($path, 1) : $path;
    }

    /**
     * メソッド一致検証配列の正規化
     *
     * @param   string|string[] $allows
     *
     * @return  mixed[]
     */
    public static function normalizeAllows($allows){
        static $cache = ["GET", ["GET" => 0]];

        if($allows !== $cache[0]){
            $cache[0]   = $allows;
            $cache[1]   = array_flip(
                array_filter(
                    (array)$allows,
                    function($method){
                        return is_string($method) && array_key_exists($method, Route::METHODS);
                    }
                )
            );
        }

        return $cache[1];
    }

    /**
     * Constructor
     *
     * @param   string  $path
     * @param   string  $host
     * @param   string[]|string $allows
     * @param   mixed[] $data
     */
    public function __construct(
        string $path,
        string $host = "*",
        $allows = "GET",
        array $data = []
    ){
        $this->path     = self::normalizePath($path);
        $this->host     = $host;
        $this->allows   = self::normalizeAllows($allows);
        $this->data     = $data;
    }

    /**
     * 指定ホストとメソッドでこのルートが有効か確認する
     *
     * @param   string  $host
     * @param   string  $method
     *
     * @return  bool
     */
    public function isEnable(string $host, string $method){
        if($method === "HEAD"){
            $method = "GET";
        }

        return fnmatch($this->host, $host)
            && array_key_exists($method, $this->allows)
        ;
    }

    /**
     * セグメントリストを取得する
     *
     * @return  Segment\Segment[]
     */
    public function getSegments(){
        return Segment\Parser::getSegments($this->path);
    }

    /**
     * パスを取得する
     *
     * @return  string
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * パスを設定する
     *
     * @param   string  $path
     *
     * @return  static
     */
    public function withPath(string $path){
        $path   = self::normalizePath($path);

        if($this->path === $path){
            return $this;
        }

        $clone          = clone $this;
        $clone->path    = $path;

        return $clone;
    }

    /**
     * ホストを取得する
     *
     * @return  string|null
     */
    public function getHost(){
        return $this->host;
    }

    /**
     * ホストを設定する
     *
     * シェルワイルドカード構文を用いる。空文字の場合は全てにマッチ。
     *
     * @param   string  $host
     *
     * @return  static
     */
    public function withHost(string $host = "*"){
        if($this->host === $host){
            return $this;
        }

        $clone          = clone $this;
        $clone->host    = $host;

        return $clone;
    }

    /**
     * 許容メソッドを取得する
     *
     * @return  string[]
     */
    public function getAllows(){
        return $this->allows;
    }

    /**
     * 許容メソッドを設定する
     *
     * @param   string[]    $allows
     *
     * @return  static
     *
     * @throws  \InvalidArgumentException
     */
    public function withAllows($allows){
        $allows = self::normalizeAllows($allows);

        if($this->allows === $allows){
            return $this;
        }

        $clone          = clone $this;
        $clone->allows  = $allows;

        return $clone;
    }

    /**
     * データを取得する
     *
     * @return  mixed[]
     */
    public function getData(){
        return $this->data;
    }

    /**
     * データを設定する
     *
     * @param   mixed[] $data
     *
     * @return  static
     */
    public function withData(array $data = []){
        $clone          = clone $this;
        $clone->data    = $data;

        return $clone;
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
     * @return  static|$this
     */
    public function withName(string $name = null){
        $name   = trim($name ?? "");
        $name   = $name === "" ? null : $name;

        if($this->name === $name){
            return $this;
        }

        $clone          = clone $this;
        $clone->name    = $name;

        return $clone;
    }
}