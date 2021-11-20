<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;
use stdClass;

class PayloadTest extends TestCase
{
    public function testInitialValueIsNull(): void
    {
        $this->assertNull((new Route('/'))->getPayload());
    }

    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testSettableAndGettable(mixed $value): void
    {
        $route = new Route('/');

        $route->payload($value);
        $this->assertSame($value, $route->getPayload());
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

    public function testOverwriteable(): void
    {
        $route = new Route('/');

        $route->payload(123);
        $this->assertSame(123, $route->getPayload());

        $route->payload('string');
        $this->assertSame('string', $route->getPayload());

        $route->payload(null);
        $this->assertNull($route->getPayload());
    }
}
