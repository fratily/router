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

    public function __construct(SegmentBuilder $builder){
    }

    /**
     * @param string $path
     * @return array
     */
    public function parse(string $path): array{
        $path       = "/" === mb_substr($path, 0, 1) ? $path : "/{$path}";
        $segments   = [];

        foreach(explode("/", $path) as $segment){
            $segments[] = $this->generateSegment($segment);
        }

        return $segments;
    }

    /**
     * @param string $segment
     * @return Segment
     */
    public function generateSegment(string $segment): Segment{

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