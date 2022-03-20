<?php

namespace Fratily\Router;

use Fratily\PathParser\Segments\PlainSegment;
use Fratily\PathParser\Segments\NamedSegment\ColonNamedSegment;
use Fratily\PathParser\Segments\SlashSegment;
use InvalidArgumentException;

/**
 * @phpstan-import-type Queries from RouteOption
 * @phpstan-type Segments array<int,SlashSegment|ColonNamedSegment|PlainSegment>
 * @phpstan-type Conf array{s:Segments,q:Queries}
 */
class ReverseRouter
{
    public const CONF_KEY_SEGMENT = 's';
    public const CONF_KEY_QUERY = 'q';

    /**
     * @param array[] $routesConfig
     *
     * @phpstan-param array<string,Conf> $routesConfig
     */
    public function __construct(
        /**
         * @var array[]
         * @phpstan-var array<string,Conf>
         */
        private array $routesConfig
    ) {
    }

    /**
     * @param string $name The route name.
     * @param string[] $pathParams The path parameters.
     * @param string[] $queryParams The query parameters.
     * @param string|null $fragment The fragment string.
     * @return string
     *
     * @phpstan-param array<string,string> $pathParams
     * @phpstan-param array<string,string> $queryParams
     * @phpstan-param non-empty-string|null $fragment
     * @phpstan-return non-empty-string
     */
    public function make(
        string $name,
        array $pathParams = [],
        array $queryParams = [],
        ?string $fragment = null
    ): string {
        if (!isset($this->routesConfig[$name])) {
            throw new InvalidArgumentException("The route with the name {$name} was not found.");
        }

        [
            self::CONF_KEY_SEGMENT => $segments,
            self::CONF_KEY_QUERY => $queries
        ] = $this->routesConfig[$name];

        // @phpstan-ignore-next-line fragment is nullable
        return static::makePath($segments, $pathParams)
            . static::makeQueryString($queries, $queryParams)
            . $fragment === null ? '' : '#' . $fragment;
    }

    /**
     * Returns the path.
     *
     * @param (SlashSegment|ColonNamedSegment|PlainSegment)[] $segments
     * @param string[] $params
     * @return string
     *
     * @phpstan-param list<SlashSegment|ColonNamedSegment|PlainSegment>  $segments
     * @phpstan-param array<string,string> $params
     * @phpstan-return non-empty-string
     */
    public static function makePath(array $segments, array $params = []): string
    {
        if (count($segments) === 0) {
            throw new InvalidArgumentException('At least one segment is required to generate the path.');
        }

        $path = '';
        foreach ($segments as $segment) {
            $path .= match (true) {
                $segment instanceof SlashSegment => '/',
                $segment instanceof PlainSegment => '/' . $segment->getSegment(),
                // phpcs:disable Generic.Files.LineLength.TooLong
                /** @phpstan-ignore-next-line ColonNamedSegment will always evaluate to true. */
                $segment instanceof ColonNamedSegment => '/' . rawurlencode($params[$segment->getName()] ?? throw new InvalidArgumentException("Must specify the path parameter {$segment->getName()}.")),
                // phpcs:enable Generic.Files.LineLength.TooLong
            };
        }

        return $path;
    }

    /**
     * Returns the query string.
     *
     * If necessary, add a question mark as a prefix.
     *
     * TODO: 今のままでは `key[abc]=1` 等が扱えない。
     * http_build_queryと同等の引数を受け付けながら処理するようにする。
     *
     * @param bool[]|null $queries
     * @param string[] $params
     * @return string
     *
     * @phpstan-param Queries $queries
     * @phpstan-param array<string,string> $params
     */
    public static function makeQueryString(?array $queries, array $params): string
    {
        /** @phpstan-var array<string,mixed> */
        $use_params = [];

        if ($queries === null) {
            $use_params = $params;
        } else {
            foreach ($queries as $queryKey => $required) {
                if ($required) {
                    // phpcs:disable Generic.Files.LineLength.TooLong
                    $use_params[$queryKey] = $params[$queryKey] ?? throw new InvalidArgumentException("Must specify query parameter {$queryKey}.");
                    // phpcs:enable Generic.Files.LineLength.TooLong
                } elseif (isset($params[$queryKey])) {
                    $use_params[$queryKey] = $params[$queryKey];
                }
            }
        }

        return count($use_params) === 0
            ? ''
            : '?' . http_build_query($use_params, encoding_type: PHP_QUERY_RFC3986);
    }
}
