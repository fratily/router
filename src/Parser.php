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
class Parser{

    /**
     * セグメントに分割する
     *
     * スラッシュで分割される。
     *
     * @param   string  $url
     *
     * @return  string[]
     */
    public static function split2segments(string $url){
        return explode("/", substr($url, 0, 1) === "/" ? substr($url, 1) : $url);
    }

    /**
     * セグメント文字列を解析する
     *
     * @param   string  $segment
     *
     * @return  mixed[]
     */
    public static function segment(string $segment){
        $type   = Router::RAW;
        $param  = null;
        $match  = $segment;

        if((bool)preg_match("/\A\{([0-9A-Z_]+)(:|\|)(.+?)\}\z/i", $segment, $m)){
            $type   = $m[2] === ":" ? Router::REG : Router::SREG;
            $param  = $m[1];
            $match  = $m[3];

            if($type === Router::REG && (bool)preg_match("/\A[0-9A-Z%_]*\z/i", $match)){
                $type   = Router::RAW;
            }
        }

        return [
            "type"  => $type,
            "param" => $param,
            "match" => $match
        ];
    }
}