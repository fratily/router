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

use Fratily\Router\Exception\InvalidSegmentException;
use Fratily\Router\Segment;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class SegmentTest extends TestCase{

    /**
     * @param   string  $segment
     * @param   string|null $expectedSame
     * @param   string|null $expectedName
     * @param   string|null $expectedRegex
     * @param   string|null $expectedFilter
     *
     * @return  void
     *
     * @dataProvider   provideGenerateSegmentInstanceData
     */
    public function testGenerateSegmentInstance(
        string $segment,
        ?string $expectedSame,
        ?string $expectedName,
        ?string $expectedRegex,
        ?string $expectedFilter
    ){
        $instance   = new Segment($segment);

        $this->assertSame($segment, $instance->getDefinition());
        $this->assertSame($expectedSame, $instance->getSame());
        $this->assertSame($expectedName, $instance->getName());
        $this->assertSame($expectedRegex, $instance->getRegex());
        $this->assertSame($expectedFilter, $instance->getFilter());
    }

    public function provideGenerateSegmentInstanceData(){
        return [
            "empty"         => ["",              "",      null,   null,    null],
            "same"          => ["same",          "same",  null,   null,    null],
            "same start {"  => ["{same",         "{same", null,   null,    null],
            "same end {"    => ["same}",         "same}", null,   null,    null],
            "name"          => ["{name}",        null,    "name", null,    null],
            "regex"         => ["{name:regex}",  null,    "name", "regex", null],
            "filter"        => ["{name@filter}", null,    "name", null,    "filter"],
        ];
    }

    /**
     * @param   string  $segment
     *
     * @return  void
     *
     * @dataProvider   provideGenerateSegmentInstanceWithInvalidSegmentData
     */
    public function testGenerateSegmentInstanceWithInvalidSegment($segment){
        $this->expectException(InvalidSegmentException::class);

        new Segment($segment);
    }

    public function provideGenerateSegmentInstanceWithInvalidSegmentData(){
        return [
            "empty"                         => ["{}"],
            "dont have name case regex"     => ["{:regex}"],
            "dont have name case filter"    => ["{@filter}"],
            "invalid name"                  => ["{0a}"],
            "invalid name 2"                => ["{aa-bb}"],
        ];
    }
}