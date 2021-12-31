<?php

namespace Fratily\Tests\Router\RouteOption;

use Fratily\Router\RouteOption;
use PHPUnit\Framework\TestCase;

class StrictCheckTrailingTest extends TestCase
{
    /**
     * @dataProvider dataProvider_settableAndGettable
     */
    public function test_settableAndGettable(bool $value): void
    {
        $option = new RouteOption();

        $nextOption = $option->strictCheckTrailing($value);
        self::assertNotSame($option, $nextOption);
        self::assertTrue($option->isStrictCheckTrailing());
        $this->assertSame($value, $nextOption->isStrictCheckTrailing());
    }

    public function dataProvider_settableAndGettable(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
