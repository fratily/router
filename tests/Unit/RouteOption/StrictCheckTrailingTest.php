<?php

namespace Fratily\Tests\Router\RouteOption;

use Fratily\Router\Route;
use Fratily\Router\RouteOption;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class StrictCheckTrailingTest extends TestCase
{
    /**
     * @dataProvider dataValidCall
     */
    public function testValidCall(bool $initial_value, bool $set_value): void
    {
        $route_option_reflection = new ReflectionClass(RouteOption::class);
        $route_option = $route_option_reflection->newInstanceWithoutConstructor();

        $strict_check_trailing_prop_reflection = $route_option_reflection->getProperty('isStrictCheckTrailing');
        $strict_check_trailing_prop_reflection->setAccessible(true);
        $strict_check_trailing_prop_reflection->setValue($route_option, $initial_value);

        $new_route_option = $route_option->strictCheckTrailing($set_value);

        $this->assertSame($initial_value, $strict_check_trailing_prop_reflection->getValue($route_option));
        $this->assertSame($set_value, $strict_check_trailing_prop_reflection->getValue($new_route_option));
    }

    /**
     * @phpstan-return list<array{bool,bool}>
     */
    public function dataValidCall(): array
    {
        return [
            [true, false],
            [false, true],
        ];
    }
}
