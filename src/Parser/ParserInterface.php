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
interface ParserInterface{

    /**
     * セグメントリストを取得する
     *
     * @param   string  $path
     *
     * @return  Segment[]
     */
    public function getSegments(string $path);

    /**
     * セグメントに分割する
     *
     * @param   string  $path
     *
     * @return  string[]
     */
    public function split2Segments(string $path);
}