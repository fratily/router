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

        $matchNode = self::nodeExplore($this->tree, $path);

        if ($matchNode === null) {
            return null;
        }

        if ($matchNode->getMatchRoute() === null || $matchNode->getSegmentIndexByName() === null) {
            throw new LogicException();
        }

        return [
            'route' => $matchNode->getMatchRoute(),
            'params' => self::makeNameParamMap($matchNode->getSegmentIndexByName(), $path),
        ];
    }

    private static function nodeExplore(Node $currentNode, ?string $remainingPath): ?Node {
        if ($remainingPath === null) {
            return $currentNode->getMatchRoute() !== null ? $currentNode : null;
        }

        foreach ($currentNode->getMatchedChildrenForSkippablePath($remainingPath) as $match) {
            ['node' => $matchNode, 'remainingPath' => $nextRemainingPath] = $match;

            $resultNode = self::nodeExplore($matchNode, $nextRemainingPath);

            if ($resultNode !== null) {
                return $resultNode;
            }
        }

        $splittedPath = explode('/', $remainingPath, 3);
        $segment = $splittedPath[1];
        $nextRemainingPath = $splittedPath[2] ?? null;

        if ($nextRemainingPath !== null) {
            $nextRemainingPath = '/' . $nextRemainingPath;
        }

        foreach ($currentNode->getMatchedChildren($segment) as $childNode) {
            $resultNode = self::nodeExplore($childNode, $nextRemainingPath);

            if ($resultNode !== null) {
                return $resultNode;
            }
        }

        return null;
    }

    /**
     * @param int[] $segmentIndexByName
     * @param string $path
     * @return string[]
     *
     * @phpstan-param array<string,int<0,max>> $segmentIndexByName
     * @phpstan-return array<string,string>
     */
    private static function makeNameParamMap(array $segmentIndexByName, string $path): array
    {
        $segments = explode('/', substr($path, 1));

        return array_map(
            fn($index) => $segments[$index] ?? throw new LogicException(),
            $segmentIndexByName
        );
    }
}
