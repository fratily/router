<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use Fratily\Router\RouteOption;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class OptionTest extends TestCase
{
    public function testValidCall(): void
    {
        $initial_option = $this->createMock(RouteOption::class);
        $set_option = $this->createMock(RouteOption::class);

        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $option_prop_reflection = $route_reflection->getProperty('option');
        $option_prop_reflection->setAccessible(true);
        $option_prop_reflection->setValue($route, $initial_option);

        $new_route = $route->option($set_option);

        $this->assertSame($initial_option, $option_prop_reflection->getValue($route));
        $this->assertSame($set_option, $option_prop_reflection->getValue($new_route));
    }
}
