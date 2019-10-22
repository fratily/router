<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author     Kento Oka <kento-oka@kentoka.com>
 * @copyright (c) Kento Oka
 * @license   MIT
 * @since     1.0.0
 */
namespace Fratily\Router\Segments;

use Fratily\Router\SegmentInterface;

/**
 *
 */
class SameSegment implements SegmentInterface
{
    /**
     * @var SameSegment[]
     */
    private static $instances = [];

    /**
     * @var string
     */
    private $segment;

    /**
     * Returns the SameSegment instance.
     *
     * This method is an implementation for a singleton pattern.
     *
     * @param string $segment The segment
     *
     * @return SameSegment
     */
    public static function instance(string $segment): SameSegment
    {
        if (!isset(self::$instances[$segment])) {
            self::$instances[$segment] = new static($segment);
        }

        return self::$instances[$segment];
    }

    /**
     * Constructor.
     *
     * @param string $segment The segment
     */
    protected function __construct(string $segment)
    {
        $this->segment = $segment;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return "same:{$this->segment}";
    }

    /**
     * {@inheritDoc}
     */
    public function match(string $segment): bool
    {
        return $this->segment === $segment;
    }
}
