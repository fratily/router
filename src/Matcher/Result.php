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

use Fratily\Router\Route;

/**
 *
 */
class Result{

    /**
     * @var bool
     */
    private $found;

    /**
     * @var mixed[]
     */
    private $parameters;

    /**
     * @var Route
     */
    private $route;

    /**
     * Constructor.
     *
     * @param bool       $found      Route is found
     * @param mixed[]    $parameters Routing path parameters
     * @param Route|null $route      The matching route
     */
    public function __construct(bool $found, array $parameters = [], Route $route = null){
        if($found && null === $route){
            throw new \InvalidArgumentException();
        }

        $this->found        = $found;
        $this->parameters   = $parameters;
        $this->route        = $route;
    }

    /**
     * Returns true if found route.
     *
     * @return bool
     */
    public function found(): bool{
        return $this->found;
    }

    /**
     * Returns the routing path parameters.
     *
     * @return mixed[]
     */
    public function getParameters(): array{
        return $this->parameters;
    }

    /**
     * Returns the found route.
     *
     * @return Route
     */
    public function getRoute(): Route{
        if(!$this->found()){
            throw new \LogicException();
        }

        return $this->route;
    }
}
