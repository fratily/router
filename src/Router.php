<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Router;

/**
 *
 */
class Router{

    private $sregex = [];

    public function getShortRegex(string $name){
        return $this->sregex[$name] ?? null;
    }

    public function hasShortRegex(string $name){
        return isset($this->sregex[$name]);
    }

    public function addShortRegex(string $name, string $class){
        if($name === ""){
            throw new \InvalidArgumentException();
        }else if(!class_exists($class)){
            throw new \InvalidArgumentException();
        }

        $ref    = new \ReflectionClass($class);

        if(!$ref->implementsInterface(ShortRegexInterface::class)){
            throw new \InvalidArgumentException();
        }

        $this->sregex[$name]    = $ref->getName();
    }

    public function addRoute(string $name, string $path, array $data){
        
    }
}