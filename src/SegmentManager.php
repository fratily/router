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
namespace Fratily\Router;

/**
 *
 */
class SegmentManager
{
    /**
     * @var SegmentInterface[]
     */
    private $segments = [];

    /**
     * @var string|null
     */
    private $default;

    /**
     * @var bool
     */
    private $isLocked = false;

    /**
     * Lock the route collector.
     *
     * @return void
     */
    public function lock(): void
    {
        $this->isLocked = true;
    }

    /**
     * Returns whether or not is locked.
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * Returns the segment instance by name.
     *
     * @param string|null $name
     *
     * @return SegmentInterface
     */
    public function getSegment(string $name = null): SegmentInterface
    {
        if (null === $name) {
            if (null === $this->getDefaultSegmentName()) {
                throw new \InvalidArgumentException();
            }

            if (!isset($this->segments[$this->getDefaultSegmentName()])) {
                throw new \LogicException();
            }

            return $this->segments[$this->getDefaultSegmentName()];
        }

        if (!$this->hasSegment($name)) {
            throw new \InvalidArgumentException();
        }

        return $this->segments[$name];
    }

    /**
     * Returns whether there are segment.
     *
     * @param string $name The segment name.
     *
     * @return bool
     */
    public function hasSegment(string $name): bool
    {
        return isset($this->segments[$name]);
    }

    /**
     * Add the segment.
     *
     * @param SegmentInterface $segment The segment
     * @param bool             $isDefault The segment is default
     *
     * @return $this
     */
    public function addSegment(SegmentInterface $segment, bool $isDefault = false): SegmentManager
    {
        if ($this->hasSegment($segment->getName())) {
            throw new \InvalidArgumentException();
        }

        $this->segments[$segment->getName()] = $segment;

        if ($isDefault) {
            $this->setDefaultSegmentName($segment->getName());
        }

        return $this;
    }

    /**
     * Remove the segment.
     *
     * @param string $name The segment name
     *
     * @return $this
     */
    public function removeSegment(string $name): SegmentManager
    {
        unset($this->segments[$name]);

        if ($this->getDefaultSegmentName() === $name) {
            $this->default = null;
        }

        return $this;
    }

    /**
     * Returns the default segment name.
     *
     * @return string|null
     */
    public function getDefaultSegmentName(): ?string
    {
        return $this->default;
    }

    /**
     * Set the default segment name.
     *
     * @param string $name The segment name
     *
     * @return $this
     */
    public function setDefaultSegmentName(string $name): SegmentManager
    {
        if (!$this->hasSegment($name)) {
            throw new \InvalidArgumentException();
        }

        $this->default = $name;

        return $this;
    }

    /**
     * Returns the matched segment names.
     *
     * @param string   $segment The segment
     * @param string[] $expectedNames The expected segment names
     *
     * @return string[]
     */
    public function getMatchSegmentNames(string $segment, array $expectedNames = null): array
    {
         $names = [];

        if (null === $expectedNames) {
            foreach ($this->segments as $segmentInstance) {
                if ($segmentInstance->match($segment)) {
                    $names[] = $segmentInstance->getName();
                }
            }
        } else {
            foreach ($expectedNames as $expectedName) {
                if (!$this->hasSegment($expectedName)) {
                    continue;
                }

                if ($this->getSegment($expectedName)->match($segment)) {
                    $names[] = $expectedName;
                }
            }
        }

         return $names;
    }
}
