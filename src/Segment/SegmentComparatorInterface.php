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
interface SegmentComparatorInterface{

    /**
     * Parse segment define text.
     *
     * @param   string|null $define
     *
     * @return  mixed   Return payload.
     *
     * @throws  \LogicException
     */
    public function parseDefine(?string $define);

    /**
     *
     *
     * @param   string  $segment    Segment text.
     * @param   mixed   $payload    Payload.
     *
     * @return  bool
     */
    public function compare(string $segment, $payload): bool;
}