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
 * @property-read   bool    $found
 * @property-read   string  $name
 * @property-read   mixed[] $params
 * @property-read   mixed[] $data
 */
class Routing{

    /**
     * @var Route
     */
    private $route;

    /**
     * @var mixed[]
     */
    private $params;

    /**
     * Constructor
     *
     * @param   string  $name
     * @param   Route   $route
     * @param   mixed[] $params
     *
     * @throws  \InvalidArgumentException
     */
    public function __construct(Route $route = null, array $params = []){
        $this->route    = $route;
        $this->params   = $params;
    }

    /**
     * Get
     *
     * @param   string  $key
     *
     * @return  mixed
     */
    public function __get($key){
        switch($key){
            case "found":
                return $this->route !== null;
            case "name":
                return $this->route !== null ? $this->route->getName() : null;
            case "data":
                return $this->route !== null ? $this->route->getData() : null;
            case "params":
                return $this->params;
        }

        return null;
    }
}