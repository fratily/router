<?php

namespace Fratily\Router\Nodes;

/**
 * Nodes that match the segment consisting of empty string.
 *
 * Used as the last node of a path ending with a slash.
 */
class BlankNode extends Node
{
    public function match(string $segment): bool
    {
        return $segment === '';
    }
}
