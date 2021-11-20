<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;

class StrictCheckTrailingTest extends TestCase
{
    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testSettableAndGettable(?bool $value): void
    {
        $route = new Route('/');

        $this->assertNull($route->isStrictCheckTrailing());
        $nextRoute = $route->strictCheckTrailing($value);
        $this->assertNull($route->isStrictCheckTrailing());
        $this->assertSame($value, $nextRoute->isStrictCheckTrailing());
    }

    public function dataProviderSettableAndGettable(): array
    {
        return [[true], [false], [null]];
    }
}
