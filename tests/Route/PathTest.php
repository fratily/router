<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use Fratily\Router\RouteOption;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testSettableAndGettable(string $value): void
    {
        $route = new Route('/', new RouteOption());

        $this->assertSame('/', $route->getPath());
        $nextRoute = $route->path($value);
        $this->assertSame('/', $route->getPath());
        $this->assertSame($value, $nextRoute->getPath());
    }

    public function dataProviderSettableAndGettable(): array
    {
        return [
            ['/'],
            ['/abc'],
            ['/abc/'],
            ['/abc/def'],
            ['/abc/def/'],
        ];
    }

    /**
     * @dataProvider dataProviderInvalidValue
     */
    public function testInvalidValue(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        $route = new Route('/', new RouteOption());

        $route->path($value);
    }

    public function dataProviderInvalidValue(): array
    {
        return [
            'empty' => [''],
            'only space' => [' '],
            'not start with slash' => ['a/bc'],
            'start with space' => [' /bc'],
            'end with space' => ['/bc '],
            'include multibyte char' => ['/bc/あ'],
            'start with consecutive slashes' => ['//abc/def'],
            'contain consecutive slashes' => ['/abc//def'],
            'end with consecutive slashes' => ['/abc/def//'],
        ];
    }
}
