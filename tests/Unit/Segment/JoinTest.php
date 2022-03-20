<?php

namespace Fratily\Tests\Router\Unit\Segment;

use Fratily\Router\Segment;
use PHPUnit\Framework\TestCase;

class JoinTest extends TestCase
{
    /**
     * @dataProvider dataValidCall
     * @phpstan-param string[] $segments
     * @phpstan-param int<0,max> $offset
     * @phpstan-param positive-int|null $limit
     */
    public function testValidCall(array $segments, int $offset, ?int $limit, string $expected): void
    {
        $this->assertSame(
            $expected,
            Segment::join($segments, $offset, $limit)
        );
    }

    /**
     * @phpstan-return list<array{string[],int<0,max>,positive-int|null,string}>
     */
    public function dataValidCall(): array
    {
        return [
            [
                ['abc', 'def', 'ghi', 'jkl', 'mno', 'pqr', 'stu', 'vwx', 'yz'],
                0,
                2,
                '/abc/def',
            ],
            [
                ['abc', 'def', 'ghi', 'jkl', 'mno', 'pqr', 'stu', 'vwx', 'yz'],
                2,
                2,
                '/ghi/jkl',
            ],
        ];
    }

    /**
     * @dataProvider dataValidCallWithoutOptionalArgs
     * @phpstan-param string[] $segments
     */
    public function testValidCallWithoutOptionalArgs(array $segments, string $expected): void
    {
        $this->assertSame($expected, Segment::join($segments));
    }

    /**
     * @phpstan-return list<array{string[],string}>
     */
    public function dataValidCallWithoutOptionalArgs(): array
    {
        return [
            [[''], '/'],
            [['abc'], '/abc'],
            [['abc', ''], '/abc/'],
            [['abc', '', ''], '/abc//'],
            [['abc', 'def'], '/abc/def'],
            [['abc', 'def', ''], '/abc/def/'],
            [
                ['abc', 'def', 'ghi', 'jkl', 'mno', 'pqr', 'stu', 'vwx', 'yz'],
                '/abc/def/ghi/jkl/mno/pqr/stu/vwx/yz'
            ],
        ];
    }
}
