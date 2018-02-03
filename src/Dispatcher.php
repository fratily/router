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
    public function dispatch(string $method, string $url, bool $searchAllow = true){
        $method     = strtoupper($method);
        $url        = substr($url, 0, 1) === "/" ? $url : "/{$url}";
        $cacheKey   = $this->getCacheKey($method, $url);
        $result     = [self::NOT_FOUND, [], []];
        
        if(($cache = $this->getResultCache($cacheKey)) !== null){
            return $cache;
        }
        
        if(isset($this->collector->getStatic()[$method][$url])){
            $result = [
                self::FOUND,
                [],
                $this->collector->getStatic()[$method][$url]
            ];
        }else{
            $search = $this->searchNode(
                array_reverse(Parser::split2segments($url)),
                $this->collector->getTree()[$method] ?? []
            );
            
            if($search !== false){
                $result = [self::FOUND, $search[0], $search[1]];
            }
        }
        
        if($result[0] === self::NOT_FOUND && $method === "HEAD"){   //  HEADメソッドの場合GETのルールにも一致する
            $getResult  = $this->dispatch("GET", $url, false);
            
            if($getResult[0] === self::FOUND){
                $result = $getResult;
            }
        }
        
        if($result[0] === self::NOT_FOUND && $searchAllow){
            $allowed    = $this->allowedMethods(
                $url,array_reverse(Parser::split2segments($url)), $method
            );
            
            if(0 < count($allowed)){
                $result = [self::METHOD_NOT_ALLOWED, $allowed, []];
            }
        }

        $this->setResultCache($cacheKey, $result);

        return $result;
    }
    
    /**
     * アクセスできるHTTPメソッドのリストを返す
     * 
     * @param   string  $url
     *      リクエストURL
     * @param   string[]    $segments
     *      リクエストURLをセグメントごとに格納したstack
     * @param   string  $masks
     *      無視するHTTPメソッド
     * 
     * @return  string[]
     */
    private function allowedMethods(string $url, array $segments, string ...$masks){
        $allowed    = [];
        
        foreach($this->collector->getStatic() as $method => $match){
            if(!in_array($method, $masks)){
                if($url === $match){
                    $allowed[]  = $method;
                }
            }
        }
            
        foreach($this->collector->getTree() as $method => $node){
            if(!in_array($method, $allowed) && !in_array($method, $masks)){
                if($this->searchNode($segments, $node)){
                    $allowed[]  = $method;
                }
            }
        }
        
        return $allowed;
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
    private function searchNode(array $segments, array $node, array $params = []){
        $end        = null;
        $segment    = array_pop($segments);
        
        foreach($node as $data){
            if(isset($data["type"]) && isset($data["rule"])){
                $match  = false;
                
                //  一致するか判定
                switch($data["type"]){
                    case RouteCollector::RAW:
                        $match  = $segment === $data["rule"];
                        break;
                    
                    case RouteCollector::REGEX:
                        $match  = (bool)preg_match("/\A{$data["rule"]}\z/", $segment);
                        break;
                    
                    case RouteCollector::SREGEX:
                        if(self::hasShortRegex($data["rule"])){
                            $match  = self::getShortRegex($data["rule"])->match($segment);
                        }
                        break;
                    default:
                        continue 2;
                }
                
                if($match){
                    if(isset($data["name"])){   //  セグメントをパラメーターに追加
                        $params[$data["name"]]  =
                            $data["type"] === RouteCollector::SREGEX
                                ? self::getShortRegex($data["rule"])->convert($segment)
                                : $segment;
                    }
                    
                    if(empty($segments)){
                        if(isset($data["end"])){
                            return [$params, $end];
                        }
                        
                        return false;
                    }
                    
                    $return = $this->searchNode($segments, $data["children"], $params);
                    
                    if($return !== false){
                        return $return;
                    }
                }
            }
        }
        
        return false;
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