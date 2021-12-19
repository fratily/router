<?php

namespace Fratily\Router;

use Fratily\Router\Nodes\Node;
use Fratily\Router\Nodes\RootNode;
use InvalidArgumentException;
use LogicException;

class Router
{
    public function __construct(private RootNode $tree)
    {
    }

    /**
     * @phpstan-return array{route:Route,params:array<string,string>}|null
     */
    public function match(string $path): ?array
    {
        if ($path !== '' && !str_starts_with($path, '/')) {
            throw new InvalidArgumentException();
        }

        if ($path === '') {
            if ($this->tree->getMatchRoute() !== null) {
                return ['route' => $this->tree->getMatchRoute(), 'params' => []];
            }

            return null;
        }

        $segments = Segment::split($path);
        $matchNode = self::nodeExplore($this->tree, $segments);

        if ($matchNode === null) {
            return null;
        }

        if ($matchNode->getMatchRoute() === null || $matchNode->getSegmentIndexByName() === null) {
            throw new LogicException();
        }

        return [
            'route' => $matchNode->getMatchRoute(),
            'params' => array_map(
                fn($index) => $segments[$index] ?? throw new LogicException(),
                $matchNode->getSegmentIndexByName()
            ),
        ];
    }

    /**
     * @param Node $currentNode
     * @param string[] $remainingSegments
     */
    private static function nodeExplore(Node $currentNode, array $remainingSegments): ?Node {
        if (count($remainingSegments) === 0) {
            return $currentNode->getMatchRoute() !== null ? $currentNode : null;
        }

        foreach ($currentNode->getMatchedChildrenForSkippablePath($remainingSegments) as $match) {
            ['node' => $matchNode, 'remainingSegments' => $nextRemainingSegments] = $match;

            $resultNode = self::nodeExplore($matchNode, $nextRemainingSegments);

            if ($resultNode !== null) {
                return $resultNode;
            }
        }

        $segment = array_slice($remainingSegments, 0, 1)[0];
        $nextRemainingSegments = array_slice($remainingSegments, 1);

        foreach ($currentNode->getMatchedChildren($segment) as $childNode) {
            $resultNode = self::nodeExplore($childNode, $nextRemainingSegments);

            if ($resultNode !== null) {
                return $resultNode;
            }
        }

        return null;
    }
}
