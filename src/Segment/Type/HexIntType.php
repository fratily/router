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
namespace Fratily\Router\Segment\Type;

/**
 *
 */
class HexIntType implements TypeInterface{

    /**
     * {@inheritdoc}
     */
    public static function valid(string $segment){
        static $cache   = [];

        if(!array_key_exists($segment, $cache)){
            if(!substr($segment, 0, 2) === "0x"){
                $cache[$segment]    = null;
            }else{
                $result = filter_var($segment, FILTER_VALIDATE_INT, FILTER_FLAG_ALLOW_HEX);

                $cache[$segment]    = $result !== false ? $result : null;
            }
        }

        return $cache[$segment];
    }
}