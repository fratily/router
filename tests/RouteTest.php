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

    private const INITIAL_NAME      = "initialName";
    private const INITIAL_PATH      = "/this/is/initial/path";
    private const INITIAL_METHODS   = ["POST", "DELETE"];
    private const INITIAL_HOST      = "initial.host";

    /**
     * @var Route
     */
    private $route;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void{
        $this->route    = new Route(
            self::INITIAL_NAME,
            self::INITIAL_PATH,
            self::INITIAL_METHODS,
            self::INITIAL_HOST
        );
    }

    public function testName(){
        $this->assertSame(self::INITIAL_NAME, $this->route->getName());

        $this->route->withName("name");
        $this->assertSame(self::INITIAL_NAME, $this->route->getName());

        $this->route    = $this->route->withName("name");
        $this->assertSame("name", $this->route->getName());
    }

    public function testPath(){
        $this->assertSame(self::INITIAL_PATH, $this->route->getPath());

        $this->route->withPath("/path");
        $this->assertSame(self::INITIAL_PATH, $this->route->getPath());

        $this->route    = $this->route->withPath("/path");
        $this->assertSame("/path", $this->route->getPath());

        $this->assertSame("/path/test", $this->route->withPath("path/test")->getPath());
    }

    public function testMethods(){
        $this->assertSame(self::INITIAL_METHODS, $this->route->getMethods());

        $this->route->withMethods(["GET", "POST", "DELETE"]);
        $this->assertSame(self::INITIAL_METHODS, $this->route->getMethods());

        $this->route    = $this->route->withMethods(["GET", "POST", "DELETE"]);
        $this->assertSame(["GET", "POST", "DELETE"], $this->route->getMethods());

        $this->route    = $this->route->withMethods(["GET", "POST", "DELETE", "DELETE"]);
        $this->assertSame(["GET", "POST", "DELETE"], $this->route->getMethods());
    }

    public function testHost(){
        $this->assertSame(self::INITIAL_HOST, $this->route->getHost());

        $this->route->withHost("host");
        $this->assertSame(self::INITIAL_HOST, $this->route->getHost());

        $this->route    = $this->route->withHost("host");
        $this->assertSame("host", $this->route->getHost());
    }

    public function testPayload(){
        $this->assertSame(null, $this->route->getPayload());

        $this->route->withPayload("payload");
        $this->assertSame(null, $this->route->getPayload());

        $this->route    = $this->route->withPayload("payload");
        $this->assertSame("payload", $this->route->getPayload());
    }
}