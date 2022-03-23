<?php

namespace Fratily\Router\Nodes;

/**
 * Root node in the routing tree.
 */
class RootNode extends Node
{
    public function __construct()
    {
        parent::__construct(null);
    }

    public function match(string $segment): bool
    {
        return true;
    }
}
