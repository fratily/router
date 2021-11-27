<?php

namespace Fratily\Router\Nodes;

/**
 * A node that matches a segment consisting of any one or more characters.
 */
class AnyNode extends Node
{
    public function match(string $segment): bool
    {
        return strlen($segment) > 0;
    }
}
