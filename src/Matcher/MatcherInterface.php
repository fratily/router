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
namespace Fratily\Router\Matcher;

/**
 *
 */
interface MatcherInterface{

    /**
     * Find a route that matches request.
     *
     * @param string $host The request host
     * @param string $method The request http method
     * @param string $path The request path
     *
     * @return Result
     */
    public function match(string $host, string $method, string $path): Result;
}
