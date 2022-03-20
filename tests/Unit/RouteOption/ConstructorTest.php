<?php

namespace Fratily\Tests\Router\Unit\RouteOption;

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
    public function testValidCall(array $value, bool $expect_strict_check_trailing): void
    {
        $route_option = new RouteOption($value);
        $reflection = new ReflectionObject($route_option);

        $strict_check_trailing_prop_reflection = $reflection->getProperty('isStrictCheckTrailing');
        $strict_check_trailing_prop_reflection->setAccessible(true);

        $this->assertSame(
            $expect_strict_check_trailing,
            $strict_check_trailing_prop_reflection->getValue($route_option)
        );
    }

    /**
     * @phpstan-return list<array{array<string,mixed>,bool}>
     */
    public function dataValidCall(): array
    {
        return [
            [['strict_check_trailing' => true], true],
            [['strict_check_trailing' => false], false],
            [[], true],
        ];
    }
}
