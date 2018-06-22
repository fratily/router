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
     * @var Parser\ParserInterface
     */
    private $parser;

    /**
     * @var string
     */
    private $path;

    /**
     * @var mixed[]
     */
    private $data   = [];

    /**
     * @var string|null
     */
    private $host   = "*";

    /**
     * @var string[]
     */
    private $allows = [
        "GET"
    ];

    private $name;

    /**
     * インスタンスを生成する
     *
     * @param   Parser\ParserInterface  $parser
     * @param   string  $path
     * @param   mixed[] $data
     * @param   string  $host
     * @param   string[]    $allows
     *
     * @return  static
     */
    public static function newInstance(
        Parser\ParserInterface $parser,
        string $path,
        array $data = [],
        string $host = "*",
        $allows = ["GET"]
    ){
        return (new static($parser, $path))
            ->withData($data)
            ->withHost($host)
            ->withAllows($allows)
        ;
    }

    /**
     * Constructor
     *
     * @param   string  $path
     */
    public function __construct(Parser\ParserInterface $parser, string $path){
        $this->parser   = $parser;
        $this->path     = $path;
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

        return fnmatch($this->host, $host) && in_array($method, $this->allows);
    }

    /**
     * セグメントリストを取得する
     *
     * @return  Parser\Segment[]
     */
    public function getSegments(){
        return $this->parser->getSegments($this->path);
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
        $host   = trim($host);

        if($host === ""){
            $host   = "*";
        }

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
        $allows = array_filter(array_unique((array)$allows), function($v){
            return array_key_exists($v, Route::METHODS);
        });

        if(empty($allows)){
            throw new \InvalidArgumentException();
        }

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
     * ルート名を取得する
     *
     * @return  string|null
     */
    public function getName(){
        return $this->name;
    }

    /**
     * ルート名を設定する
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