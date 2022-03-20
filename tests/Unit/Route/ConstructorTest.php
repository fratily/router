<?php

namespace Fratily\Tests\Router\Unit\Route;

use Fratily\Router\Route;
use Fratily\Router\RouteOption;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class ConstructorTest extends TestCase
{
    /**
     * @dataProvider dataValidCall
     */
    public function testValidCall(string $path): void
    {
        $option = $this->createMock(RouteOption::class);

        $route = new Route($path, $option);
        $reflection = new ReflectionObject($route);

        $path_prop_reflection = $reflection->getProperty('path');
        $option_prop_reflection = $reflection->getProperty('option');

        $path_prop_reflection->setAccessible(true);
        $option_prop_reflection->setAccessible(true);

        $this->assertSame($path, $path_prop_reflection->getValue($route));
        $this->assertSame($option, $option_prop_reflection->getValue($route));
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
    public function testInvalidCall(string $path, string $exception_message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exception_message);

        new Route($path, $this->createMock(RouteOption::class));
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
