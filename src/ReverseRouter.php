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
class ReverseRouter{

    /**
     * @var Route
     */
    private $route;

    /**
     * Constructor
     */
    public function __construct(Route $route){
        $this->route    = $route;
    }

    /**
     * URLパスを生成する
     *
     * @param   mixed[] $params
     * @param   bool    $addQuery
     * @param   mixed[] $options
     *
     * @return  string
     *
     * @throws  \InvalidArgumentException
     */
    public function createPath(
        array $params = [],
        bool $addQuery = true,
        array $options = []
    ){
        $path       = [];
        $uses       = [];
        $query      = "";
        $options    = $options + [
            "numeric_prefix"    => null,
            "enc_type"          => PHP_QUERY_RFC3986
        ];

        foreach($this->route->getSegments() as $segment){
            if(Segment\Segment::MODE_SAME === $segment->getMode()){
                $path[] = $segment->getModeData();
                continue;
            }

            if(null === $segment->getName()){
                throw new \LogicException;  // パラメータ部分に名前がついていないのでどうしようもない
            }

            if(!array_key_exists($segment->getName(), $params)){
                throw new \LogicException;  // 埋め込めるパラメータがない
            }

            if(!$segment->isMatch($params[$segment->getName()])){
                throw new \LogicException;  // パラメータの値がセグメントに対して正しくない
            }

            $uses[$segment->getName()]  = true;
            $path[]                     = $params[$segment->getName()];
        }

        $params = array_filter(
            $params,
            function($k) use ($uses){
                return !array_key_exists($k, $uses);
            },
            ARRAY_FILTER_USE_KEY
        );

        if(!empty($params) && $addQuery){
            $query  = "?" . http_build_query(
                $params, $options["numeric_prefix"], null, $options["enc_type"]
            );
        }

        return "/" . implode("/", $path) . $query;
    }
}