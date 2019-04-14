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
class Route implements RouteInterface{

    public const DEFAULT_HOST       = "*";

    public const DEFAULT_METHODS    = ["GET"];

    public const ALLOW_METHODS      = [
        "GET",
        "HEAD",
        "POST",
        "PUT",
        "PATCH",
        "DELETE",
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string|null
     */
    private $host;

    /**
     * @var string[]|null
     */
    private $methods;

    /**
     * @var mixed[]
     */
    private $payload    = [];

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, string $path){
        if("/" !== mb_substr($path, 0, 1)){
            $path   = "/" . $path;
        }

        $this->name = $name;
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function withName(string $name): RouteInterface{
        if($this->name === $name){
            return $this;
        }

        $clone          = clone $this;
        $clone->name    = $name;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath(): string{
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function withPath(string $path): RouteInterface{
        if("/" !== mb_substr($path, 0, 1)){
            $path   = "/" . $path;
        }

        if($this->path === $path){
            return $this;
        }

        $clone          = clone $this;
        $clone->path    = $path;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost(): string{
        return $this->host ?? static::DEFAULT_HOST;
    }

    /**
     * {@inheritDoc}
     */
    public function withHost(string $host): RouteInterface{
        if($this->host === $host){
            return $this;
        }

        $clone          = clone $this;
        $clone->host    = $host;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods(): array{
        return $this->methods ?? static::DEFAULT_METHODS;
    }

    /**
     * {@inheritDoc}
     */
    public function withMethods(string ...$methods): RouteInterface{
        $clone          = clone $this;
        $clone->methods = $methods;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function getPayload(): array{
        return $this->payload;
    }

    /**
     * {@inheritDoc}
     */
    public function withPayload(array $payload): RouteInterface{
        $clone          = clone $this;
        $clone->payload = $payload;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters(): array{
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameter(string $key, $default = null){
        return $this->parameters[$key] ?? $default;
    }

    /**
     * {@inheritDoc}
     */
    public function withParameters(array $parameters): RouteInterface{
        $clone              = clone $this;
        $clone->parameters  = $parameters;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withParameter(string $key, $value): RouteInterface{
        if(
            array_key_exists($key, $this->parameters)
            && $this->parameters[$key] === $value
        ){
            return $this;
        }

        $clone              = clone $this;
        $parameters         = $this->parameters;
        $parameters[$key]   = $value;
        $clone->parameters  = $parameters;

        return $clone;
    }

}