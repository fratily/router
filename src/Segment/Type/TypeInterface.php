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
interface TypeInterface{

    /**
     * 値の一致確認を行い変換を行う
     *
     * 返した値がアプリケーション内部で使えるセグメント値。
     * nullを返した場合は一致しなかったと判断される。
     *
     * @param   string  $segment
     */
    public static function valid(string $segment);
}