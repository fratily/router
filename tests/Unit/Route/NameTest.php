<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NameTest extends TestCase
{
    /**
     * @dataProvider dataValidCall
     */
    public function testValidCall(?string $name): void
    {
        $route_reflection = new ReflectionClass(Route::class);
        $route = $route_reflection->newInstanceWithoutConstructor();

        $name_prop_reflection = $route_reflection->getProperty('name');
        $name_prop_reflection->setAccessible(true);

        $new_route = $route->name($name);

        $this->assertNull($name_prop_reflection->getValue($route));
        $this->assertSame($name, $name_prop_reflection->getValue($new_route));
    }

    /**
     * @phpstan-return list<array{string|null}>
     */
    public function dataValidCall(): array
    {
        return [
            [null],
            [''],
            ['name'],
            ['あ'],
        ];
    }
}
