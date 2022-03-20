<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GetNameTest extends TestCase
{
    public function testValidCall(): void
    {
        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $name_prop_reflection = $route_reflection->getProperty('name');
        $name_prop_reflection->setAccessible(true);
        $name_prop_reflection->setValue($route, 'value');

        $this->assertSame('value', $name_prop_reflection->getValue($route));
    }
}
