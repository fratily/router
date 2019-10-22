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
class MatchedRoute
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $parameters;

    /**
     * @var mixed|null
     */
    private $payload;

    /**
     * Constructor.
     *
     * @param string     $name The route name
     * @param string[]   $parameters The segment parameters
     * @param mixed|null $payload The route payload
     */
    public function __construct(string $name, array $parameters, $payload)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->payload = $payload;
    }

    /**
     * Returns the route name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the segment parameters.
     *
     * @return string[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the segment parameter.
     *
     * @param string $name The parameter name
     *
     * @return string
     */
    public function getParameter(string $name): string
    {
        if (!$this->hasParameter($name)) {
            throw new \LogicException();
        }

        return $this->parameters[$name];
    }

    /**
     * Returns whether there are segment parameters.
     *
     * @param string $name The parameter name
     *
     * @return bool
     */
    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Returns the route payload.
     *
     * @return mixed|null
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
