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
}