<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use Fratily\Router\RouteOption;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PathTest extends TestCase
{
    /**
     * @dataProvider dataValidCall
     */
    public function testValidCall(string $value): void
    {
        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $path_prop_reflection = $route_reflection->getProperty('path');
        $path_prop_reflection->setAccessible(true);
        $path_prop_reflection->setValue($route, '/'); // To avoid accessed before initialization

        $new_route = $route->path($value);

        $this->assertSame('/', $path_prop_reflection->getValue($route));
        $this->assertSame($value, $path_prop_reflection->getValue($new_route));
    }

    /**
     * @phpstan-return list<array{string}>
     */
    public function dataValidCall(): array
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
     * @dataProvider dataInvalidCall
     */
    public function testInvalidCall(string $value, string $exception_message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exception_message);

        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $path_prop_reflection = $route_reflection->getProperty('path');
        $path_prop_reflection->setAccessible(true);

        $route->path($value);
    }

    /**
     * @phpstan-return array<string,array{string,string}>
     */
    public function dataInvalidCall(): array
    {
        return [
            'empty' => ['', 'The path must not be an empty string.'],
            'only space' => [' ', 'The path must not start or end with a space.'],
            'start with space' => [' /bc', 'The path must not start or end with a space.'],
            'end with space' => ['/bc ', 'The path must not start or end with a space.'],
            'not start with slash' => ['a/bc', 'The path must start with a slash.'],
            'start with consecutive slashes' => ['//abc/def', 'The path must not contain consecutive slashes.'],
            'contain consecutive slashes' => ['/abc//def', 'The path must not contain consecutive slashes.'],
            'end with consecutive slashes' => ['/abc/def//', 'The path must not contain consecutive slashes.'],
            'include multibyte char' => ['/bc/あ', 'The path must not contain multibyte characters.'],
        ];
    }
}
