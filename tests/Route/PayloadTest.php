<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;
use stdClass;

class PayloadTest extends TestCase
{
    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testSettableAndGettable(mixed $value): void
    {
        $route = new Route('/');

        $this->assertNull($route->getPayload());
        $nextRoute = $route->payload($value);
        $this->assertNull($route->getPayload());
        $this->assertSame($value, $nextRoute->getPayload());
    }

    public function dataProviderSettableAndGettable(): iterable
    {
        $values = [
            'true' => true,
            'false' => false,
            'int' => 123,
            'float' => 123.456,
            'string' => 'string',
            'array' => [],
            'object' => new stdClass(),
            'closure' => fn() => 123,
            'resource' => STDIN,
            'null' => null,
        ];

        foreach ($values as $key => $value) {
            yield $key => [$value];
        }

        foreach ($values as $key => $value) {
            yield "array({$key})" => [[$value]];
        }

        foreach ($values as $key => $value) {
            yield "fn(): {$key}" => [fn() => $value];
        }
    }
}
