<?php

namespace Fratily\Router;

use Fratily\PathParser\Segments\PlainSegment;
use Fratily\PathParser\Segments\NamedSegment\ColonNamedSegment;
use Fratily\PathParser\Segments\SlashSegment;
use Fratily\Router\Nodes\AnyNode;
use Fratily\Router\Nodes\BlankNode;
use Fratily\Router\Nodes\Node;
use Fratily\Router\Nodes\RegexNode;
use Fratily\Router\Nodes\RootNode;
use Fratily\Router\Nodes\SameNode;
use InvalidArgumentException;

class RouterBuilder
{
    /**
     * @var Route[]
     */
    private array $routes;

    /**
     * @param Route[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function build(): Router
    {
        $rootNode = new RootNode();
        foreach ($this->routes as $route) {
            $parentNode = $rootNode;
            /** @phpstan-var array<string,int<0,max>> */
            $segmentIndexByName = [];
            foreach (Segment::parse($route->getPath()) as $index => $segment) {
                if ($segment instanceof ColonNamedSegment) {
                    $segmentIndexByName[$segment->getName()] = $index;
                }

                $node = self::findConflictChildNode($parentNode, $segment);

                if ($node === null) {
                    $node = self::createNode($parentNode, $segment);
                    $parentNode->addChild($node);
                }

                $parentNode = $node;
            }

            $lastNode = $parentNode;

            if ($lastNode->getMatchRoute() !== null) {
                throw new InvalidArgumentException();
            }

            $lastNode->markAsTheEnd($route, $segmentIndexByName);
            $isStrictCheckTrailing = $route->getOption()->isStrictCheckTrailing();
            if (!$isStrictCheckTrailing) {
                if ($lastNode instanceof BlankNode) {
                    if ($lastNode->getParent() === null) {
                        throw new InvalidArgumentException();
                    }
                    if ($lastNode->getParent()->getMatchRoute() !== null) {
                        throw new InvalidArgumentException($route->getPath());
                    }

                    $lastNode->getParent()->markAsTheEnd($route, $segmentIndexByName);
                } else {
                    $node = new BlankNode($lastNode);
                    $node->markAsTheEnd($route, $segmentIndexByName);
                    $lastNode->addChild($node);
                }
            }
        }

        return new Router($rootNode);
    }

    private static function findConflictChildNode(
        Node $node,
        PlainSegment|ColonNamedSegment|SlashSegment $segment
    ): ?Node
    {
        if ($segment instanceof SlashSegment) {
            foreach ($node->getChildren() as $childNode) {
                if ($childNode instanceof BlankNode) {
                    return $childNode;
                }
            }

            return null;
        }

        if ($segment instanceof PlainSegment) {
            $segment_without_slash = substr($segment->getSegment(), 1);

            foreach ($node->getChildren() as $childNode) {
                if ($childNode instanceof SameNode && $childNode->getSegment() === $segment_without_slash) {
                    return $childNode;
                }
            }

            return null;
        }

        if ($segment->getOption() === null) {
            foreach ($node->getChildren() as $childNode) {
                if ($childNode instanceof AnyNode) {
                    return $childNode;
                }
            }

            return null;
        }

        foreach ($node->getChildren() as $childNode) {
            if ($childNode instanceof RegexNode && $childNode->getRegex() === $segment->getOption()) {
                return $childNode;
            }
        }

        return null;
    }

    private static function createNode(Node $parentNode, PlainSegment|ColonNamedSegment|SlashSegment $segment): Node
    {
        if ($segment instanceof SlashSegment) {
            return new BlankNode($parentNode);
        }

        if ($segment instanceof PlainSegment) {
            return new SameNode($parentNode, substr($segment->getSegment(), 1));
        }

        if ($segment->getOption() === null) {
            return new AnyNode($parentNode);
        }

        return new RegexNode($parentNode, $segment->getOption());
    }

    public function buildReverseRouter(): ReverseRouter
    {
        $routeConfigs = [];
        foreach ($this->routes as $route) {
            if ($route->getName() === null) {
                continue;
            }

            $routeConfigs[$route->getName()] = [
                ReverseRouter::CONF_KEY_SEGMENT => Segment::parse($route->getPath()),
                ReverseRouter::CONF_KEY_QUERY => $route->getOption()->getQueries(),
            ];
        }

        return new ReverseRouter($routeConfigs);
    }
}
