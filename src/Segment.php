<?php

namespace Fratily\Router;

use Fratily\PathParser\PathParser;
use Fratily\PathParser\Segments\PlainSegment;
use Fratily\PathParser\Segments\NamedSegment\ColonNamedSegment;
use Fratily\PathParser\Segments\SlashSegment;
use InvalidArgumentException;

class Segment
{
    /** @phpstan-var class-string<SlashSegment> */
    public const SEGMENT_CLASS_SLASH = SlashSegment::class;
    /** @phpstan-var class-string<ColonNamedSegment> */
    public const SEGMENT_CLASS_NAMED = ColonNamedSegment::class;
    /** @phpstan-var class-string<PlainSegment> */
    public const SEGMENT_CLASS_PLAIN = PlainSegment::class;

    private const SEGMENT_CLASSES = [
        Segment::SEGMENT_CLASS_SLASH,
        Segment::SEGMENT_CLASS_NAMED,
        Segment::SEGMENT_CLASS_PLAIN,
    ];

    /**
     *
     * @return (SlashSegment|ColonNamedSegment|PlainSegment)[]
     * @phpstan-return array<int<0,max>,SlashSegment|ColonNamedSegment|PlainSegment>
     */
    public static function parse(string $path): array
    {
        /** @phpstan-var array<int<0,max>,SlashSegment|ColonNamedSegment|PlainSegment> */
        $segments = PathParser::parse($path, self::SEGMENT_CLASSES);

        foreach ($segments as $segment) {
            if ($segment instanceof ColonNamedSegment && $segment->getOption() !== null) {
                // TODO: オプションが 正規表現として正しいことを確認する。
            }
        }

        return $segments;
    }

    /**
     * @return string[]
     */
    public static function split(string $path): array
    {
        if (!str_starts_with($path, '/')) {
            throw new InvalidArgumentException();
        }

        return explode('/', substr($path, 1));
    }

    /**
     * @param string[] $segments
     * @param int $offset
     * @param int $limit
     * @return string
     *
     * @phpstan-param int<0,max> $offset
     * @phpstan-param positive-int|null $limit
     */
    public static function join(array $segments, int $offset = 0, int $limit = null): string
    {
        return '/' . implode('/', array_slice($segments, $offset, $limit));
    }
}
