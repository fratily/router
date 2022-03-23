<?php

namespace Fratily\Router\Nodes;

use InvalidArgumentException;

/**
 * Nodes that match segments that match a particular string.
 */
class SameNode extends Node
{
    private string $segment;

    public function __construct(?Node $parent, string $segment)
    {
        parent::__construct($parent);

        if ($segment === '' || str_contains($segment, '/')) {
            throw new InvalidArgumentException();
        }

        $this->segment = $segment;
    }

    public function match(string $segment): bool
    {
        return $this->segment === $segment;
    }

    public function getSegment(): string
    {
        return $this->segment;
    }
}
