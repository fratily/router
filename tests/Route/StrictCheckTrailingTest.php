<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;

class StrictCheckTrailingTest extends TestCase
{
    public function testInitialValueIsNull(): void
    {
        $this->assertNull((new Route('/', ['GET']))->isStrictCheckTrailing());
    }

    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testSettableAndGettable(?bool $value): void
    {
        $route = new Route('/', ['GET']);

        $route->strictCheckTrailing($value);
        $this->assertSame($value, $route->isStrictCheckTrailing());
    }

    public function dataProviderSettableAndGettable(): array
    {
        return [[true], [false], [null]];
    }

    public function testOverwriteable(): void
    {
        $route = new Route('/', ['GET']);

        $route->strictCheckTrailing(true);
        $this->assertTrue($route->isStrictCheckTrailing());

        $route->strictCheckTrailing(false);
        $this->assertFalse($route->isStrictCheckTrailing());

        $route->strictCheckTrailing(null);
        $this->assertNull($route->isStrictCheckTrailing());
    }
}
