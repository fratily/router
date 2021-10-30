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
    public function testSettable(
        string $path,
        array $methods,
        ?string $name,
        string $expectedPath,
        array $expectedMethods,
        ?string $expectedName
    ): void
    {
        $route = new Route($path, $methods, $name);

        $this->assertSame($expectedPath, $route->getPath());
        $this->assertSame($expectedMethods, $route->getMethods());
        $this->assertSame($expectedName, $route->getName());
    }

    public function dataProviderSettable(): iterable
    {
        $pathPatterns = [
            ['/', '/'],
            ['/abc', '/abc'],
            ['/abc/', '/abc/'],
        ];
        $methodsPatterns = [
            [['string'], ['string']],
            [['string', 'GET'], ['string', 'GET']],
        ];
        $namePatterns = [
            ['name', 'name'],
            [' name', ' name'],
            ['name/name1.name', 'name/name1.name'],
            ['', ''],
            [null, null]
        ];

        foreach ($pathPatterns as $pathPattern) {
            foreach ($methodsPatterns as $methodsPattern) {
                foreach ($namePatterns as $namePattern) {
                    yield [
                        $pathPattern[0],
                        $methodsPattern[0],
                        $namePattern[0],
                        $pathPattern[1],
                        $methodsPattern[1],
                        $namePattern[1],
                    ];
                }
            }
        }
    }
}
