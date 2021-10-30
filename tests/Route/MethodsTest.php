<?php

namespace Fratily\Tests\Router\Route;

use Fratily\Router\Route;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MethodsTest extends TestCase
{
    /**
     * @dataProvider dataProviderSettableAndGettable
     */
    public function testSettableAndGettable(array $value, array $expected): void
    {
        $route = new Route('/', ['GET']);

        $route->methods($value);
        $this->assertSame($expected, $route->getMethods());
    }

    public function dataProviderSettableAndGettable(): array
    {
        return [
            [['string'], ['string']],
            [['string', 'GET'], ['string', 'GET']],
        ];
    }

    /**
     * @dataProvider dataProviderInvalidValue
     */
    public function testInvalidValue(array $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        $route = new Route('/', ['GET']);

        $route->methods($value);
    }

    public function dataProviderInvalidValue(): array
    {
        return [
            'empty' => [[]],
            'include not string' => [['a', 123]],
            'include empty string' => [['', 'b']],
            'include only space string' => [['b', ' ']],
            'start with space' => [['b', ' GET']],
            'end with space' => [['b', 'GET ']],
            'not unique' => [['a', 'a']]
        ];
    }

    public function testOverwriteable(): void
    {
        $route = new Route('/', ['GET']);

        $route->methods(['POST']);
        $this->assertSame(['POST'], $route->getMethods());

        $route->methods(['POST', 'GET']);
        $this->assertSame(['POST', 'GET'], $route->getMethods());

        $route->methods(['GET']);
        $this->assertSame(['GET'], $route->getMethods());
    }
}
