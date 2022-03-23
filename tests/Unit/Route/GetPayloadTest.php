<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GetPayloadTest extends TestCase
{
    public function testValidCall(): void
    {
        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $payload_prop_reflection = $route_reflection->getProperty('payload');
        $payload_prop_reflection->setAccessible(true);
        $payload_prop_reflection->setValue($route, 'value');

        $this->assertSame('value', $payload_prop_reflection->getValue($route));
    }
}
