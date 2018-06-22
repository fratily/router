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
namespace Fratily\Router\Parser;

/**
 *
 */
class StandardParser implements ParserInterface{

    /**
     * {@inheritdoc}
     */
    public function getSegments(string $path){
        $path       = substr($path, 0, 1) === "/" ? substr($path, 1) : $path;
        $segments   = [];

        foreach($this->split2segments($path) as $segment){
            $segments[]  = static::getSegment($segment);
        }

        return $segments;
    }

    /**
     * {@inheritdoc}
     */
    public function split2segments(string $path){
        $path   = substr($path, 0, 1) === "/" ? substr($path, 1) : $path;

        return explode("/", $path);
    }

    /**
     * セグメント文字列を解析する
     *
     * @param   string  $segment
     *
     * @return  mixed[]
     */
    private static function getSegment(string $segment){
        static $cache   = [];

        $name   = null;
        $type   = Segment::T_PLAIN;

        if(mb_substr($segment, 0, 1) === "{" && mb_substr($segment, -1) === "}"){
            $type   = Segment::T_REGEX;

            if((bool)preg_match("/\A\{(?:([A-Z_][0-9A-Z_]*):)?(.+?)\}\z/i", $segment, $m)){
                if($m[1] !== ""){
                    $name   = $m[1];
                }

                $segment    = $m[2];
            }
        }

        if(!array_key_exists($segment, $cache)){
            if($type === Segment::T_REGEX
                && (bool)preg_match("/\A([0-9A-Z-_]|%[0-9A-Z]{2})*\z/i", $segment)
            ){
                $type   = Segment::T_PLAIN;
            }

            $cache[$segment]    = new Segment($type, $segment);
        }

        return $cache[$segment]->withName($name);
    }
}