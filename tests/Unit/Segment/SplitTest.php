<?php

namespace Fratily\Tests\Router\Unit\Segment;

use Fratily\Router\Segment;
use PHPUnit\Framework\TestCase;

class SplitTest extends TestCase
{
    /**
     * @dataProvider dataValidCall
     */
    public function testValidCall(string $path, array $expected): void
    {
        $this->assertSame($expected, Segment::split($path));
    }

    /**
     * @phpstan-return list<array{string,string[]}>
     */
    public function dataValidCall(): array
    {
        return [
            ['/', ['']],
            ['/abc', ['abc']],
            ['/abc/', ['abc', '']],
            ['/abc//', ['abc', '', '']],
            ['/abc/def', ['abc', 'def']],
            ['/abc/def/', ['abc', 'def', '']],
            ['/abc/def/ghi/jkl/mno/pqr/stu/vwx/yz', ['abc', 'def', 'ghi', 'jkl', 'mno', 'pqr', 'stu', 'vwx', 'yz']],
        ];
    }
}
