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

    private static $shortRegexes    = [];

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
     * @param   RouteCollector  $routes
     * @param   CacheInterface  $cache
     *      ルーティング結果をキャッシュする
     * @param   int $ttl
     *      キャッシュが有効な秒数
     * @param   string  $prefix
     *      キャッシュの名前に追加する文字列。
     *      この値を変更すれば過去のキャッシュは使用されなくなる。
     */
    public function __construct(
        RouteCollector $routes,
        CacheInterface $cache = null,
        int $ttl = 600,
        string $prefix = ""
    ){

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

    }
}