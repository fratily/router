<?php

namespace Fratily\Router\Nodes;

/**
 * Nodes that match segments that match regular expressions.
 */
class RegexNode extends Node
{
    private string $regex;

    public function __construct(?Node $parent, string $regex)
    {
        parent::__construct($parent);

        $this->regex = $regex;
    }

    public function match(string $segment): bool
    {
        return preg_match('/\A' . $this->regex . '\z/', $segment) === 1;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }
}
