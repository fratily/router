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
     * @var mixed[]
     */
    private $segments   = [];

    /**
     * @var string[]
     */
    private $params     = [];

    /**
     * Constructor
     */
    public function __construct(string $path){
        $i  = 0;
        foreach(Parser::split2segments($path) as $segment){
            $segment    = Parser::segment($segment);

            if($segment["type"] !== Parser::RAW){
                $this->segments[$i] = null;
                $this->params[$i]   = $segment["param"];
            }else{
                $this->segments[$i] = $segment["match"];
            }

            $i++;
        }
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
        $query      = "";
        $options    = $options + [
            "numeric_prefix"    => null,
            "enc_type"          => PHP_QUERY_RFC3986
        ];

        foreach($this->segments as $key => $segment){
            if($segment !== null){
                $path[] = $segment;
            }else{
                $param  = $this->params[$key];

                if(!isset($params[$param])){
                    throw new \InvalidArgumentException();
                }else if(!is_scalar($params[$param])
                    && !(
                        is_object($params[$param])
                        && method_exists($params[$param], "__toString")
                    )
                ){
                    throw new \InvalidArgumentException();
                }

                $path[] = (string)$params[$param];

                unset($params[$param]);
            }
        }

        $path   = "/" . implode("/", $path);

        if(!empty($params) && $addQuery){
            $query  = http_build_query(
                $params, $options["numeric_prefix"], null, $options["enc_type"]
            );

            if($query !== ""){
                $query  = "?" . $query;
            }
        }

        return $path . $query;
    }
}