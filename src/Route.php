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
class Route{

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
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $path;

    /**
     * @var string
     */
    private $host   = self::DEFAULT_HOST;

    /**
     * @var string[]
     */
    private $methods    = self::DEFAULT_METHODS;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * Get name.
     *
     * @return  string|null
     */
    public function getName(): ?string{
        return $this->name;
    }

    /**
     * With name.
     *
     * @param   string  $name
     *
     * @return  static
     */
    public function withName(string $name): self{
        if($this->name === $name){
            return $this;
        }

        if("" === $name){
            throw new \InvalidArgumentException(
                "Empty string cant use to route name."
            );
        }

        $clone          = clone $this;
        $clone->name    = $name;

        return $clone;
    }

    /**
     * Get path.
     *
     * @return  string|null
     */
    public function getPath(): ?string{
        return $this->path;
    }

    /**
     * With path.
     *
     * @param   string  $path
     *
     * @return  static
     */
    public function withPath(string $path): self{
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
     * Get host.
     *
     * @return  string
     */
    public function getHost(): string{
        return $this->host;
    }

    /**
     * With host.
     *
     * @param   string  $host
     *
     * @return  static
     */
    public function withHost(string $host): self{
        if($this->host === $host){
            return $this;
        }

        $clone          = clone $this;
        $clone->host    = $host;

        return $clone;
    }

    /**
     * Get methods.
     *
     * @return  string[]
     */
    public function getMethods(): array{
        return $this->methods;
    }

    /**
     * With methods.
     *
     * @param   string[]    $methods
     *
     * @return  static
     */
    public function withMethods(array $methods): self{
        foreach($methods as $method){
            if(!in_array($method, self::ALLOW_METHODS)){
                throw new \InvalidArgumentException(
                    "{$method} cant use route allow methods. useful methods is "
                        . implode(", ", self::ALLOW_METHODS)
                );
            }
        }

        $clone          = clone $this;
        $clone->methods = $methods;

        return $clone;
    }

    /**
     * Get payload.
     *
     * @return  mixed
     */
    public function getPayload(){
        return $this->payload;
    }

    /**
     * With payload.
     *
     * @param   mixed   $payload
     *
     * @return  static
     */
    public function withPayload($payload): self{
        $clone          = clone $this;
        $clone->payload = $payload;

        return $clone;
    }
}