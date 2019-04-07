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

use Fratily\Router\Node;
use Fratily\Router\Segment;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class NodeTest extends TestCase{

    /**
     * @var Segment
     */
    private $segment;

    /**
     * {@inheritdoc}
     *
     * @throws  \ReflectionException
     */
    protected function setUp(): void{
        /**
         * @var Segment $segment
         */
        $this->segment  = $this->createMock(Segment::class)
            ->method("getDefinition")->willReturn("")
            ->method("getSame")->willReturn("")
            ->method("getName")->willReturn(null)
            ->method("getRegex")->willReturn(null)
            ->method("getFilter")->willReturn(null)
        ;
    }

    public function testGetParent(){
        $rootNode   = new Node($this->segment, null);
        $childNode  = new Node($this->segment, $rootNode);

        $this->assertSame(null, $rootNode->getParent());
        $this->assertSame($rootNode, $childNode->getParent());
    }

    public function testGetSegment(){
        $node   = new Node($this->segment, null);

        $this->assertSame($this->segment, $node->getSegment());
    }
}