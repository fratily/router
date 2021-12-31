<?php

namespace Fratily\Tests\Router\RouteOption;

use Fratily\Router\RouteOption;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type OptionArray from RouteOption
 * @phpstan-import-type PartialOptionArray from RouteOption
 */
class ConstructorTest extends TestCase
{
    public function test_initializeWithoutUserSpecifiedOpotion(): void
    {
        $option = new RouteOption();

        self::assertTrue($option->isStrictCheckTrailing());
    }

    /**
     * @dataProvider dataProvider_initialize
     *
     * @phpstan-param PartialOptionArray $userSpecifiedOptions
     * @phpstan-param OptionArray $expectedOptions
     */
    public function test_initialize(
        array $userSpecifiedOptions,
        array $expectedOptions
    ): void {
        $option = new RouteOption($userSpecifiedOptions);

        self::assertSame($expectedOptions['strict_check_trailing'], $option->isStrictCheckTrailing());
    }

    /**
     * @phpstan-return array<array{PartialOptionArray,OptionArray}>
     */
    public function dataProvider_initialize(): array
    {
        return [
            [[], ['strict_check_trailing' => true]],
            [['strict_check_trailing' => true], ['strict_check_trailing' => true]],
            [['strict_check_trailing' => false], ['strict_check_trailing' => false]],
        ];
    }

    /**
     * @dataProvider dataProvider_initializeWithInherit
     *
     * @phpstan-param OptionArray $defaultOptions
     * @phpstan-param PartialOptionArray $userSpecifiedOptions
     * @phpstan-param OptionArray $expectedOptions
     */
    public function test_initializeWithInherit(
        array $defaultOptions,
        array $userSpecifiedOptions,
        array $expectedOptions
    ): void {
        $defaultOptionsCode = var_export($defaultOptions, true);
        $userSpecifiedOptionsCode = var_export($userSpecifiedOptions, true);
        // FIXME: Don't use eval! But if you can pay attention, you can use it.
        $option = eval(
            <<<PHP
            return new class({$userSpecifiedOptionsCode}) extends \Fratily\Router\RouteOption {
                protected const DEFAULT_OPTIONS = {$defaultOptionsCode};
            };
            PHP
        );

        self::assertSame($expectedOptions['strict_check_trailing'], $option->isStrictCheckTrailing());
    }

    /**
     * @phpstan-return array<array{OptionArray,PartialOptionArray,OptionArray}>
     */
    public function dataProvider_initializeWithInherit(): array
    {
        return [
            [
                ['strict_check_trailing' => false],
                [],
                ['strict_check_trailing' => false],
            ],
            [
                ['strict_check_trailing' => false],
                ['strict_check_trailing' => true],
                ['strict_check_trailing' => true],
            ],
            [
                ['strict_check_trailing' => false],
                ['strict_check_trailing' => false],
                ['strict_check_trailing' => false],
            ],
        ];
    }
}
