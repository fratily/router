<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author     Kento Oka <kento-oka@kentoka.com>
 * @copyright (c) Kento Oka
 * @license   MIT
 * @since     1.0.0
 */
namespace Fratily\Router;

/**
 *
 */
class Route
{
    public const GET    = "GET";
    public const POST   = "POST";
    public const PUT    = "PUT";
    public const PATCH  = "PATCH";
    public const DELETE = "DELETE";

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string[]
     */
    private $methods;

    /**
     * @var mixed|null
     */
    private $payload;

    /**
     * Constructor.
     *
     * @param string $name The name
     * @param string $path The path
     * @param string[] $methods The http methods
     */
    public function __construct(
        string $name,
        string $path,
        array $methods = null
    ) {
        $this
            ->setName($name)
            ->setPath($path)
            ->setMethods($methods ?? [self::GET])
        ;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name The name
     *
     * @return $this
     */
    protected function setName(string $name): Route
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Return an instance with the specified name.
     *
     * @param string $name The name
     *
     * @return $this
     */
    public function withName(string $name): Route
    {
        if ($this->name === $name) {
            return $this;
        }

        return (clone $this)->setName($name);
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the path.
     *
     * @param string $path The path. Path must be percent encoded
     *
     * @return $this
     */
    protected function setPath(string $path): Route
    {
        $this->path = "/" !== mb_substr($path, 0, 1) ? "/{$path}" : $path;

        return $this;
    }

    /**
     * Return an instance with the specified path.
     *
     * @param string $path The path. Path must be percent encoded
     *
     * @return $this
     */
    public function withPath(string $path): Route
    {
        if ($this->path === $path) {
            return $this;
        }

        return (clone $this)->setPath($path);
    }

    /**
     * Returns the methods.
     *
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Set the methods.
     *
     * @param string[] $methods The methods.
     *
     * @return $this
     */
    protected function setMethods(array $methods): Route
    {
        foreach ($methods as $index => $method) {
            if (!is_string($method)) {
                throw new \InvalidArgumentException();
            }
        }

        $methods = array_values(array_unique($methods));

        $this->methods  = $methods;

        return $this;
    }

    /**
     * Return an instance with the specified methods.
     *
     * @param string[] $methods The methods
     *
     * @return $this
     */
    public function withMethods(array $methods): Route
    {
        if ($this->methods === $methods) {
            return $this;
        }

        return (clone $this)->setMethods($methods);
    }

    /**
     * Returns the payload.
     *
     * @return mixed|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set the payload.
     *
     * @param mixed|null $payload The payload.
     *
     * @return $this
     */
    protected function setPayload($payload): Route
    {
        $this->payload  = $payload;

        return $this;
    }

    /**
     * Return an instance with the specified payload.
     *
     * @param mixed|null $payload The payload
     *
     * @return $this
     */
    public function withPayload($payload): Route
    {
        if ($this->payload === $payload) {
            return $this;
        }

        return (clone $this)->setPayload($payload);
    }
}
