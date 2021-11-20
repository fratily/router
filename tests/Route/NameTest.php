<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testInitialValueIsNull(): void
    {
        $this->assertNull((new Route('/'))->getName());
    }

    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testGettable(?string $name): void
    {
        $route = new Route('/', $name);

        $this->assertSame($name, $route->getName());
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
