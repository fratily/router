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
class ReverseRouter{

    /**
     * @var mixed[]
     */
    private $segments   = [];

    /**
     * Constructor
     */
    public function __construct(string $path){
        
    }

    /**
     * URLパスを生成する
     *
     * @param   mixed[] $params
     * @param   bool    $addQuery
     */
    public function createPath(array $params = [], bool $addQuery = true){

    }
}