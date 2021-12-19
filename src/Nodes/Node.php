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
     * @var array Node[][]
     * @phpstan-var array<positive-int,array<string,Node>>
     */
    private array $childrenForSkippablePath = [];

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

    public function addChild(Node $childNode): void
    {
        if ($childNode->getParent() !== $this) {
            throw new InvalidArgumentException();
        }

        $this->children[] = $childNode;

        $checkTargetNode = $childNode;
        $skippablePath = '';
        $skippableSegmentCount = 0;
        while ($checkTargetParentNode = $checkTargetNode->getParent()) {
            if (!$checkTargetNode instanceof BlankNode && !$checkTargetNode instanceof SameNode) {
                break;
            }

            $segment = $checkTargetNode instanceof SameNode ? $checkTargetNode->getSegment() : '';
            $skippablePath = '/' . $segment . $skippablePath;
            $skippableSegmentCount += 1;

            if (isset($checkTargetParentNode->childrenForSkippablePath[$skippableSegmentCount][$skippablePath])) {
                throw new InvalidArgumentException();
            }

            $checkTargetParentNode->childrenForSkippablePath[$skippableSegmentCount][$skippablePath] = $childNode;
            krsort($checkTargetParentNode->childrenForSkippablePath, SORT_NUMERIC);

            $checkTargetNode = $checkTargetParentNode;
        }
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
        $remainingSegments = explode('/', substr($remainingPath, 1));
        $remainingSegmentsCount = count($remainingSegments);

        foreach ($this->childrenForSkippablePath as $skippableSegmentsCount => $nodeBySkippablePath) {
            if ($remainingSegmentsCount < $skippableSegmentsCount) {
                continue;
            }

            $skippableRemainingPath = '/' . implode('/', array_slice($remainingSegments, 0, $skippableSegmentsCount));

            if (isset($nodeBySkippablePath[$skippableRemainingPath])) {
                yield [
                    'node' => $nodeBySkippablePath[$skippableRemainingPath],
                    'remainingPath' => implode('/', array_slice($remainingSegments, $skippableSegmentsCount))
                ];
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
