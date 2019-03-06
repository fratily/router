<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento-oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Tests\Router;

use Fratily\Router\Segment;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class SegmentTest extends TestCase{

    /**
     * @param   string  $segment
     * @param   string|null $expectedName
     * @param   string|null $expectedComparator
     * @param   string|null $expectedSame
     *
     * @return  void
     *
     * @dataProvider   provideGenerateSegmentInstanceData
     */
    public function testGenerateSegmentInstance(
        string $segment,
        ?string $expectedName,
        ?string $expectedComparator,
        ?string $expectedSame
    ){
        $instance   = new Segment($segment);

        $this->assertSame($segment, $instance->getDefinition());
        $this->assertSame($expectedName, $instance->getName());
        $this->assertSame($expectedComparator, $instance->getComparator());
        $this->assertSame($expectedSame, $instance->getSame());
    }

    public function provideGenerateSegmentInstanceData(){
        return [
            "name only"           => [":name",            "name", null,         null],
            "comparator only"     => ["@comparator",      null,   "comparator", null],
            "same only"           => ["same",             null,   null,         "same"],
            "name and comparator" => [":name@comparator", "name", "comparator", null],
            "escaped colon(name)" => ["\\:name",          null,   null,         ":name"],
            "same with at sign"   => ["\\@same",          null,   null,         "@same"],
            "at sign in same"     => ["same@comparator",  null,   null,         "same@comparator"],
            "colon in same"       => ["same:name",        null,   null,         "same:name"],
        ];
    }
}