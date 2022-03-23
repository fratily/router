<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GetPathTest extends TestCase
{
    public function testValidCall(): void
    {
        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $path_prop_reflection = $route_reflection->getProperty('path');
        $path_prop_reflection->setAccessible(true);
        $path_prop_reflection->setValue($route, 'invalid value');

        $this->assertSame('invalid value', $path_prop_reflection->getValue($route));
    }
}
