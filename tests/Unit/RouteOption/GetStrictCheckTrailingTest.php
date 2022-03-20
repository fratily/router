<?php

namespace Fratily\Tests\Router\RouteOption;

use Fratily\Router\Route;
use Fratily\Router\RouteOption;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GetStrictCheckTrailingTest extends TestCase
{
    /**
     * @dataProvider dataValidCall
     */
    public function testValidCall(bool $value): void
    {
        $route_option_reflection = new ReflectionClass(RouteOption::class);
        $route_option = $route_option_reflection->newInstanceWithoutConstructor();

        $strict_check_trailing_prop_reflection = $route_option_reflection->getProperty('isStrictCheckTrailing');
        $strict_check_trailing_prop_reflection->setAccessible(true);
        $strict_check_trailing_prop_reflection->setValue($route_option, $value);

        $this->assertSame($value, $strict_check_trailing_prop_reflection->getValue($route_option));
    }

    /**
     * @phpstan-return list<array{bool}>
     */
    public function dataValidCall(): array
    {
        return [[true], [false]];
    }
}
