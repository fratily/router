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
namespace Fratily\Tests\Router;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class RouteTest extends TestCase{

    /**
     * @var Route
     */
    private $route;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void{
        $this->route    = new Route;
    }

    public function testName(){
        $this->assertSame(null, $this->route->getName());

        $this->route->withName("name");
        $this->assertSame(null, $this->route->getName());

        $this->route    = $this->route->withName("name");
        $this->assertSame("name", $this->route->getName());

        $sameRoute  = $this->route->withName("name");
        $this->assertSame($this->route, $sameRoute);
    }

    public function testPath(){
        $this->assertSame(null, $this->route->getPath());

        $this->route->withPath("/path");
        $this->assertSame(null, $this->route->getPath());

        $this->route    = $this->route->withPath("/path");
        $this->assertSame("/path", $this->route->getPath());

        $sameRoute  = $this->route->withPath("/path");
        $this->assertSame($this->route, $sameRoute);

        $sameRoute  = $this->route->withPath("path");
        $this->assertSame($this->route, $sameRoute);
    }

    public function testHost(){
        $this->assertSame(Route::DEFAULT_HOST, $this->route->getHost());

        $this->route->withHost("host");
        $this->assertSame(Route::DEFAULT_HOST, $this->route->getHost());

        $this->route    = $this->route->withHost("host");
        $this->assertSame("host", $this->route->getHost());

        $sameRoute  = $this->route->withHost("host");
        $this->assertSame($this->route, $sameRoute);
    }

    public function testMethods(){
        $this->assertSame(Route::DEFAULT_METHODS, $this->route->getMethods());

        $this->route->withMethods(["GET", "POST", "DELETE"]);
        $this->assertSame(Route::DEFAULT_METHODS, $this->route->getMethods());

        $this->route    = $this->route->withMethods(["GET", "POST", "DELETE"]);
        $this->assertSame(["GET", "POST", "DELETE"], $this->route->getMethods());

        $sameRoute  = $this->route->withMethods(["GET", "POST", "DELETE"]);
        $this->assertNotSame($this->route, $sameRoute);
    }

    public function testPayload(){
        $this->assertSame(null, $this->route->getPayload());

        $this->route->withPayload("payload");
        $this->assertSame(null, $this->route->getPayload());

        $this->route    = $this->route->withPayload("payload");
        $this->assertSame("payload", $this->route->getPayload());

        $sameRoute  = $this->route->withPayload("payload");
        $this->assertNotSame($sameRoute, $this->route);
    }
}