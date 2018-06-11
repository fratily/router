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
interface ShortRegexInterface{

    /**
     * URLのセグメントに一致するか検証する
     *
     * @param   string  $segment
     *      検証するセグメント
     *
     * @return  bool
     */
    public static function match(string $segment): bool;

    /**
     * 一致したセグメントを任意の値に書き換える
     *
     * @param   string  $segment
     *      書き換えるセグメント
     * @return  mixed
     */
    public static function convert(string $segment);
}