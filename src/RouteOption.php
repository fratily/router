<?php

namespace Fratily\Router;

use InvalidArgumentException;

/**
 * @phpstan-type Queries array<string,bool>|null
 * @phpstan-type OptionArray array{strict_check_trailing:bool, queries:Queries, query_required_default:bool}
 * @phpstan-type PartialOptionArray array{strict_check_trailing?:bool,queries?:Queries, query_required_default?:bool}
 */
class RouteOption
{
    private bool $isStrictCheckTrailing;

    /**
     * @var bool[]|null
     * @phpstan-var Queries|null
     */
    private ?array $queries;

    private bool $queryConfigRequiredDefault;

    /**
     * @var array The setting used when the initial value is not specified.
     *            To overwrite, inherit the class and overwrite this constant.
     * @phpstan-var OptionArray
     */
    protected const DEFAULT_OPTIONS = [
        'strict_check_trailing' => true,
        'queries' => null,
        'query_required_default' => false,
    ];

    /**
     * @param array $options
     *
     * @phpstan-param PartialOptionArray $options
     */
    public function __construct(array $options = []) {
        // phpcs:disable
        $this->isStrictCheckTrailing = $options['strict_check_trailing'] ?? static::DEFAULT_OPTIONS['strict_check_trailing'];
        $this->queries = $options['queries'] ?? static::DEFAULT_OPTIONS['queries'];
        $this->queryConfigRequiredDefault = $options['query_required_default'] ?? static::DEFAULT_OPTIONS['query_required_default'];
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

    /**
     * Returns a list of query parameters to allow when reverse routing.
     *
     * @return bool[]|null An associative array where the key is the query name,
     *                     and the value is a boolean whether a parameter is needed.
     *
     * @phpstan-return Queries
     */
    public function getQueries(): ?array
    {
        return $this->queries;
    }

    /**
     * Specifies the query parameters to allow when reverse routing.
     *
     * $queries examples:
     * - ['param1' => true, 'param2' => false, 'param3']
     *   - param1: This is an absolutely necessary parameter for reverse routing.
     *   - param2: This is a parameter that does not need to be specified for reverse routing.
     *   - param3: This is a parameter that does not need to be specified for reverse routing. (Depends on the setting of query_required_default)
     * - null
     *   - If NULL is specified, all parameters specified during reverse routing are allowed.
     *
     * @param (bool|string)[]|null $queries
     * @return static Returns a new object with changed settings.
     *
     * @phpstan-param array<int|string,string|bool>|null $queries
     */
    public function queries(?array $queries): static
    {
        if ($queries === null) {
            $clone = clone $this;
            $clone->queries = null;
            return $clone;
        }

        $transformedQueries = [];
        foreach ($queries as $key => $value) {
            if (is_int($key) && is_string($value)) {
                if ($value === '') {
                    throw new InvalidArgumentException();
                }

                $transformedQueries[$value] = $this->queryConfigRequiredDefault;
            }

            if (is_string($key) && is_bool($value)) {
                if ($key === '') {
                    throw new InvalidArgumentException();
                }

                $transformedQueries[$key] = $value;
            }

            throw new InvalidArgumentException();
        }

        $clone = clone $this;
        $clone->queries = $transformedQueries;
        return $clone;
    }
}
