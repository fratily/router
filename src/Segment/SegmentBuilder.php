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
namespace Fratily\Router\Segment;

/**
 *
 */
class SegmentBuilder{

    /**
     * @var SegmentComparatorInterface[]
     */
    private $comparators    = [];

    public function build(string $segmentText): Segment{
        $segment    = new Segment();

        if(":" === mb_substr($segmentText, 0, 1)){
            return $segment->withName(mb_substr($segmentText, 1));
        }

        if("\\" === mb_substr($segmentText, 0, 1)){
            $segmentText    = mb_substr($segmentText, 1);
        }

        return $segment->withSame($segmentText);
    }

    /**
     * Add comparator.
     *
     * @param   string  $name
     * @param   SegmentComparatorInterface  $comparator
     *
     * @return  $this
     */
    public function addComparator(
        string $name,
        SegmentComparatorInterface $comparator
    ): self{
        $this->comparators[$name]   = $comparator;

        return $this;
    }
}