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
interface RouteInterface{

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * With name.
     *
     * @param string $name
     *
     * @return RouteInterface
     */
    public function withName(string $name): RouteInterface;

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * With path.
     *
     * @param string $path
     *
     * @return RouteInterface
     */
    public function withPath(string $path): RouteInterface;

    /**
     * Get host.
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * With host.
     *
     * @param string $host
     *
     * @return RouteInterface
     */
    public function withHost(string $host): RouteInterface;

    /**
     * Get method.
     *
     * @return string[]
     */
    public function getMethods(): array;

    /**
     * With method.
     *
     * @param string ...$methods
     *
     * @return RouteInterface
     */
    public function withMethods(string ...$methods): RouteInterface;

    /**
     * Get payload.
     *
     * @return mixed[]
     */
    public function getPayload(): array;

    /**
     * With payload.
     *
     * @param mixed[] $payload
     *
     * @return RouteInterface
     */
    public function withPayload(array $payload): RouteInterface;

    /**
     * Get parameters.
     *
     * @return mixed[]
     */
    public function getParameters(): array;

    /**
     * Get parameter.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter(string $key, $default = null);

    /**
     * With parameters.
     *
     * @param mixed[] $parameters
     *
     * @return RouteInterface
     */
    public function withParameters(array $parameters): RouteInterface;

    /**
     * With parameter.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return RouteInterface
     */
    public function withParameter(string $key, $value): RouteInterface;
}