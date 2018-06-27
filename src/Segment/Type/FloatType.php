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
class FloatType implements TypeInterface{

    /**
     * {@inheritdoc}
     */
    public static function valid(string $segment){
        static $cache   = [];

        if(!array_key_exists($segment, $cache)){
            $result = filter_var($segment, FILTER_VALIDATE_FLOAT);

            $cache[$segment]    = $result !== false ? $result : null;
        }

        return $cache[$segment];
    }
}