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
     * @var string
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
     * @return SegmentInterface|null
     */
    public function getSegment(string $name = null): ?SegmentInterface
    {
        if (null === $name) {
            if (null === $this->getDefaultSegmentName()) {
                return null;
            }

            if (!isset($this->segments[$this->getDefaultSegmentName()])) {
                throw new \LogicException();
            }

            return $this->segments[$this->getDefaultSegmentName()];
        }

        return $this->segments[$name] ?? null;
    }

    public function hasSegment(string $name): bool
    {
        return isset($this->segments[$name]);
    }

    protected function setSegment(SegmentInterface $segment): SegmentManager
    {
        if ($this->hasSegment($segment->getName())) {
            throw new \InvalidArgumentException();
        }

        $this->segments[$segment->getName()] = $segment;

        return $this;
    }

    public function removeSegment(string $name): SegmentManager
    {
        unset($this->segments[$name]);

        if (null !== $this->getDefaultSegmentName()) {
            $this->default = null;
        }

        return $this;
    }

    public function getDefaultSegmentName(): ?string
    {
        return $this->default;
    }

    protected function setDefaultSegmentName(string $name): SegmentManager
    {
        if (!$this->hasSegment($name)) {
            throw new \InvalidArgumentException();
        }

        $this->default = $name;

        return $this;
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
        if (isset($this->segments[$segment->getName()])) {
            throw new \InvalidArgumentException();
        }

        $this->setSegment($segment);

        if ($isDefault) {
            $this->setDefaultSegmentName($segment->getName());
        }

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
