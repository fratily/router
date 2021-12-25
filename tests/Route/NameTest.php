<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testGettable(?string $name): void
    {
        $route = new Route('/', $name);

        $this->assertSame($name, $route->getName());
    }

    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testSettableAndGettable(?string $value): void
    {
        $route = new Route('/');

        $this->assertNull($route->getName());
        $nextRoute = $route->name($value);
        $this->assertNull($route->getName());
        $this->assertSame($value, $nextRoute->getName());
    }

    public function dataProviderSettableAndGettable(): array
    {
        return [
            ['name'],
            [' name '],
            ['name1/name2/name3'],
            [null],
        ];
    }
}
