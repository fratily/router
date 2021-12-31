<?php

namespace Fratily\Router;

/**
 * @phpstan-type OptionArray array{strict_check_trailing:bool}
 * @phpstan-type PartialOptionArray array{strict_check_trailing?:bool}
 */
class RouteOption
{
    private bool $isStrictCheckTrailing;

    /**
     * @var array The setting used when the initial value is not specified.
     *            To overwrite, inherit the class and overwrite this constant.
     * @phpstan-var OptionArray
     */
    protected const DEFAULT_OPTIONS = [
        'strict_check_trailing' => true,
    ];

    /**
     * @param array $options
     *
     * @phpstan-param PartialOptionArray $options
     */
    public function __construct(array $options = []) {
        // phpcs:disable
        $this->isStrictCheckTrailing = $options['strict_check_trailing'] ?? static::DEFAULT_OPTIONS['strict_check_trailing'];
        // phpcs:enable
    }

    /**
     * Returns whether to compare the end of the path exactly (with or without slashes).
     *
     * @return bool If TRUE, compare the end of the path exactly.
     */
    public function isStrictCheckTrailing(): bool
    {
        return $this->isStrictCheckTrailing;
    }

    /**
     * Specifies whether to compare the end of the path exactly (with or without slashes).
     *
     * @param bool $isStrictCheckTrailing If TRUE, compare the end of the path exactly.
     * @return static Returns a new object with changed settings.
     */
    public function strictCheckTrailing(bool $isStrictCheckTrailing): static
    {
        $clone = clone $this;
        $clone->isStrictCheckTrailing = $isStrictCheckTrailing;
        return $clone;
    }
}
