<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

class PayloadTest extends TestCase
{
    /**
     * @dataProvider dataValidCall
     */
    public function testValidCall(mixed $payload): void
    {
        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $payload_prop_reflection = $route_reflection->getProperty('payload');
        $payload_prop_reflection->setAccessible(true);

        $new_route = $route->payload($payload);

        $this->assertNull($payload_prop_reflection->getValue($route));
        $this->assertSame($payload, $payload_prop_reflection->getValue($new_route));
    }

    /**
     * @phpstan-return list<array{mixed}>
     */
    public function dataValidCall(): array
    {
        return [
            [null],
            [''],
            [123],
            [new stdClass()],
        ];
    }
}
