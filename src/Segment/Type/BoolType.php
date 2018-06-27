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
class BoolType implements TypeInterface{

    /**
     * {@inheritdoc}
     */
    public static function valid(string $segment){
        static $cache   = [];

        if(!array_key_exists($segment, $cache)){
            if(filter_var($segment, FILTER_VALIDATE_BOOLEAN)){
                $cache[$segment]    = true;
            }else if($segment !== ""
                && filter_var($segment, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === false
            ){
                $cache[$segment]    = false;
            }else{
                $cache[$segment]    = null;
            }
        }

        return $cache[$segment];
    }
}