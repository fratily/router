<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use Fratily\Router\RouteOption;
use PHPUnit\Framework\TestCase;

class ConstructorTest extends TestCase
{
    /**
     * @dataProvider dataProviderSettable
     */
    public function testSettable(string $path, string $expectedPath): void
    {
        $route = new Route($path, new RouteOption());

        $this->assertSame($expectedPath, $route->getPath());
    }

    public function dataProviderSettable(): iterable
    {
        return [
            ['/', '/'],
            ['/abc', '/abc'],
            ['/abc/', '/abc/'],
        ];
    }
}
