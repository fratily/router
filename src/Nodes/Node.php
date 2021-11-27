<?php

namespace Fratily\Router\Nodes;

use Fratily\Router\Route;
use InvalidArgumentException;

abstract class Node
{
    private ?Node $parent;

    /** @var Node[] */
    private array $children = [];

    /**
     * @var array Node[]
     * @phpstan-var array<string,Node>
     */
    private array $childrenForSkippablePath = [];

    /**
     * @var int[] List of key lengths sorted in descending order.
     * @phpstan-var positive-int[]
     */
    private array $skippablePathLengths = [];

    private ?Route $matchRoute = null;

    /**
     * @var int[]|null
     * @phpstan-var array<string,int<0,max>>|null
     */
    private ?array $segmentIndexByName = null;

    public function __construct(?Node $parent)
    {
        $this->parent = $parent;
    }

    public function addChild(Node $node): void
    {
        if ($node->parent !== $this) {
            throw new InvalidArgumentException();
        }

        $this->children[] = $node;
    }

    public function addChildForSkippablePath(string $skippablePath, Node $node): void
    {
        if (isset($this->childrenForSkippablePath[$skippablePath])) {
            throw new InvalidArgumentException();
        }

        if (($length = strlen($skippablePath)) === 0) {
            throw new InvalidArgumentException();
        }


        if (!str_starts_with($skippablePath, '/')) {
            throw new InvalidArgumentException();
        }

        $this->childrenForSkippablePath[$skippablePath] = $node;
        $this->skippablePathLengths[] = $length;

        $this->skippablePathLengths = array_unique($this->skippablePathLengths);
        rsort($this->skippablePathLengths, SORT_NUMERIC);
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * Returns a child nodes.
     *
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Returns the node that matches the segment.
     *
     * @param string $segment
     * @return Node[]
     *
     * @phpstan-return iterable<Node>
     */
    public function getMatchedChildren(string $segment): iterable
    {
        foreach ($this->children as $child) {
            if ($child->match($segment)) {
                yield $child;
            }
        }
    }

    /**
     * Returns the descendant node that pre-matches the path and the remaining paths when that node is matched.
     *
     * @param string $remainingPath Remaining paths starting with a slash.
     * @return array[]
     *
     * @phpstan-return iterable<array{node:Node,remainingPath:string|null}>
     */
    public function getMatchedChildrenForSkippablePath(string $remainingPath): iterable
    {
        foreach ($this->skippablePathLengths as $length) {
            if (strlen($remainingPath) < $length) {
                continue;
            }

            $comparePath = substr($remainingPath, 0, $length);
            $nextRemainingPath = substr($remainingPath, $length);

            if ($nextRemainingPath === '') {
                $nextRemainingPath = null;
            }

            if ($nextRemainingPath !== null && !str_starts_with($nextRemainingPath, '/')) {
                continue;
            }

            if (isset($this->childrenForSkippablePath[$comparePath])) {
                yield ['node' => $this->childrenForSkippablePath[$comparePath], 'remainingPath' => $nextRemainingPath];
            }
        }
    }

    /**
     * Set the route to end on this node.
     *
     * @param Route $matchRoute
     * @param int[] $segmentIndexByName The segment name and index map.
     * @phpstan-param array<string,int<0,max>> $segmentIndexByName
     */
    public function markAsTheEnd(Route $matchRoute, array $segmentIndexByName): void
    {
        $this->matchRoute = $matchRoute;
        $this->segmentIndexByName = $segmentIndexByName;
    }

    /**
     * Returns the route that matches the path ending at this node.
     *
     * @return Route|null
     */
    public function getMatchRoute(): ?Route
    {
        return $this->matchRoute;
    }

    /**
     * Returns the segment name and index map.
     *
     * @return int[]|null
     * @phpstan-return array<string,int<0,max>>|null
     */
    public function getSegmentIndexByName(): ?array
    {
        return $this->segmentIndexByName;
    }

    /**
     * Returns whether the specified segment matches this node.
     *
     * @param string $segment
     * @return bool
     */
    abstract public function match(string $segment): bool;
}
