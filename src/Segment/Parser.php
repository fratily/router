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
class Parser{

    /**
     * セグメントリストを取得する
     *
     * @param   string  $path
     *
     * @return  Segment[]
     */
    public static function getSegments(string $path){
        $segments   = [];
        $i          = 1;

        foreach(explode("/", $path) as $segment){
            $segments[]  = static::getSegment($segment, $i++);
        }

        return $segments;
    }

    /**
     * セグメント文字列を解析する
     *
     * @param   string  $segment
     * @param   int $cnt
     *
     * @return  mixed[]
     */
    private static function getSegment(string $segment, int $cnt){
        $name       = null;
        $instance   = null;

        if(substr($segment, 0, 1) === ":"){
            $name   = substr($segment, 1);
            $type   = "";

            if(($pos = strpos($name, "@")) !== false){
                $type   = substr($name, $pos + 1);
                $name   = substr($name, 0, $pos);
            }

            $name   = trim($name);
            $type   = trim($type);

            $name   = $name === "" ? "_p{$cnt}" : $name;
            $type   = $type === "" ? "any" : $type;

            $instance   = Segment::createTypeMode($type, $name);
        }else{
           $instance    = Segment::createSameMode($segment);
        }

        return $instance;
    }
}