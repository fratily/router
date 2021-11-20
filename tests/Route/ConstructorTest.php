<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConstructorTest extends TestCase
{
    /**
     * @dataProvider dataProviderSettable
     */
    public function testSettable(string $path, ?string $name, string $expectedPath, ?string $expectedName): void
    {
        $route = new Route($path, $name);

        $this->assertSame($expectedPath, $route->getPath());
        $this->assertSame($expectedName, $route->getName());
    }

    public function dataProviderSettable(): iterable
    {
        $pathPatterns = [
            ['/', '/'],
            ['/abc', '/abc'],
            ['/abc/', '/abc/'],
        ];
        $namePatterns = [
            ['name', 'name'],
            [' name', ' name'],
            ['name/name1.name', 'name/name1.name'],
            ['', ''],
            [null, null]
        ];

        foreach ($pathPatterns as $pathPattern) {
            foreach ($namePatterns as $namePattern) {
                yield [
                    $pathPattern[0],
                    $namePattern[0],
                    $pathPattern[1],
                    $namePattern[1],
                ];
            }
        }
    }
}
