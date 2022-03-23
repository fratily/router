<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use Fratily\Router\RouteOption;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GetOptionTest extends TestCase
{
    public function testValidCall(): void
    {
        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $option = $this->createMock(RouteOption::class);

        $option_prop_reflection = $route_reflection->getProperty('option');
        $option_prop_reflection->setAccessible(true);
        $option_prop_reflection->setValue($route, $option);

        $this->assertSame($option, $option_prop_reflection->getValue($route));
    }
}
