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

use Psr\SimpleCache\CacheInterface;

/**
 *
 */
class Dispatcher{

    const FOUND                 = 0;
    const NOT_FOUND             = 1;
    const METHOD_NOT_ALLOWED    = 2;

    /**
     * 登録されたShortRegexのリスト
     *
     * @var ShortRegex[]
     */
    private static $shortRegexes    = [];

    /**
     * @var RouteCollector
     */
    private $collector;

    /**
     * @var CacheInterface|null
     */
    private $cache;

    /**
     * @var int
     */
    private $cacheTTL;

    /**
     * @var string
     */
    private $cachePrefix;

    /**
     * ShortRegexを返す
     *
     * @param   string  $modifier
     *      ルーティングルール内でShortRegexを識別する修飾子
     *
     * @return  ShortRegexInterface
     */
    public static function getShortRegex(string $modifier){
        if(!isset(self::$shortRegexes[$modifier])){
            throw new \InvalidArgumentException();
        }

        return self::$shortRegexes[$modifier];
    }

    /**
     * ShortRegexが定義されているか返す
     *
     * @param   string  $modifier
     *      ルーティングルール内でShortRegexを識別する修飾子
     *
     * @return  bool
     */
    public static function hasShortRegex(string $modifier){
        return isset(self::$shortRegexes[$modifier]);
    }

    /**
     * ShortRegexを追加する
     *
     * @param   string  $modifier
     *      ルーティングルール内でShortRegexを識別する修飾子
     * @param   ShortRegexInterface $instance
     *      ShortRegexの実装クラスのインスタンス
     */
    public static function addShortRegex(string $modifier, ShortRegexInterface $instance){
        self::$shortRegexes[$modifier]  = $instance;
    }

    /**
     * ShortRegexを削除する
     *
     * @param   string  $modifier
     *      ルーティングルール内でShortRegexを識別する修飾子
     *
     * @return  void
     */
    public static function removeShortRegex(string $modifier){
        if(!isset(self::$shortRegexes[$modifier])){
            throw new \InvalidArgumentException();
        }

        unset(self::$shortRegexes[$modifier]);
    }

    /**
     * Constructor
     *
     * @param   RouteCollector  $collector
     * @param   CacheInterface  $cache
     *      ルーティング結果をキャッシュする
     * @param   int $ttl
     *      キャッシュが有効な秒数
     * @param   string  $prefix
     *      キャッシュの名前に追加する文字列。
     *      この値を変更すれば過去のキャッシュは使用されなくなる。
     */
    public function __construct(
        RouteCollector $collector,
        CacheInterface $cache = null,
        int $ttl = 600,
        string $prefix = ""
    ){
        $this->collector    = $collector;
        $this->cache        = $cache;
        $this->cacheTTL     = $ttl;
        $this->cachePrefix  = $prefix;
    }

    /**
     * 指定されたHTTPメソッドとURL文字列に一致するルールを探し、結果を返す
     *
     * @param   string  $method
     *      HTTPメソッド
     * @param   string  $url
     *      リクエストURL。スキーム名から始まるURIではなくパスであることに注意。
     *
     * @return  mixed[]
     *      $return[0]にルーティング結果、$return[1]にパラメーターリストを
     *      保有する配列を返します。ルーティング結果がMETHOD_NOT_ALLOWEDの場合、
     *      $return[1]には受容されるHTTPメソッドのリストが格納される。
     */
    public function dispatch(string $method, string $url){
        $method     = strtoupper($method);
        $url        = substr($url, 0, 1) === "/" ? $url : "/{$url}";
        $cacheKey   = $this->getCacheKey($method, $url);
        $cache      = $this->getResultCache($cacheKey);

        if($cache !== null){
            return $cache;
        }

        $static = $this->collector->getStatic();

        if(isset($static[$method][$url])){
            $result = [
                self::FOUND,
                $static[$method][$url]
            ];
        }else{
            //  do something

            $result = [
                self::NOT_FOUND
            ];
        }


        $this->setResultCache($cacheKey, $result);

        return $result;
    }

    /**
     * キャッシュする場合のキー
     *
     * @param   string  $method
     * @param   string  $url
     *
     * @return  string
     */
    private function getCacheKey(string $method, string $url){
        return "fratily.router."
            . hash("md5", $this->cachePrefix . $method . $url);
    }

    /**
     * ルーティング結果をキャッシュから取得する
     *
     * @param   string  $key
     *      キャッシュのキー
     *
     * @return  mixed[]|null
     */
    private function getResultCache(string $key){
        if($this->cache !== null && $this->cache->has($key)){
            $result = $this->cache->get($key);

            if(is_array($result)){
                return $result;
            }
        }

        return null;
    }

    /**
     * ルーティング結果をキャッシュする
     *
     * @param   string  $key
     *      キャッシュのキー
     * @param   mixed[] $result
     *      ルーティング結果
     */
    private function setResultCache(string $key, array $result){
        if($this->cache !== null){
            $this->cache->set($key, $result);
        }
    }
}