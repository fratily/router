<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento-oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Router;

use Fratily\Router\Exception\InvalidSegmentException;
use Fratily\Router\Node\AnyNode;
use Fratily\Router\Node\Custom\DigitNode;
use Fratily\Router\Node\NodeInterface;
use Fratily\Router\Node\RegexNode;
use Fratily\Router\Node\SameNode;
use Fratily\Router\Node\WildcardNode;

class NodeManager implements NodeManagerInterface{

    protected const SEGMENT_REGEX = "/\A([A-Z_][0-9A-Z_]*)(?:(@|:|#)([^[:cntrl:]]+))?\z/i";

    /**
     * @var string[]
     */
    private $customNodes    = [
        "d"     => DigitNode::class,
        "digit" => DigitNode::class,
        "int"   => DigitNode::class,
    ];

    /**
     * Get custom node class name.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getCustomNodeClass(string $name): ?string{
        return $this->customNodes[$name] ?? null;
    }

    /**
     * Add custom node class name.
     *
     * @param string $name
     * @param string $class
     *
     * @return NodeManager
     */
    public function addCustomNodeClass(string $name, string $class): self{
        if(!is_subclass_of($class, NodeInterface::class)){
            throw new \InvalidArgumentException();
        }

        $this->customNodes[$name]   = $class;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(
        string $segment,
        ?NodeInterface $parent
    ): NodeInterface{
        if("{" !== mb_substr($segment, 0, 1) || "}" !== mb_substr($segment, -1)){
            return $this->generateSame(rawurldecode($segment), $parent);
        }

        if(1 !== preg_match(self::SEGMENT_REGEX, mb_substr($segment, 1, -1), $m)){
            throw new InvalidSegmentException("'{$segment}' is invalid segment.");
        }

        $name   = $m[1];
        $type   = $m[2] ?? null;
        $conf   = $m[3] ?? null;

        if(null === $type){
            return $this->generateAny($parent);
        }

        if(":" === $type){
            return $this->generateRegex($conf, $parent, $name);
        }

        if("#" === $type){
            return $this->generateWildcard($conf, $parent, $name);
        }

        if("@" !== $type){
            throw new InvalidSegmentException();
        }

        if(null === $this->getCustomNodeClass($conf)){
            throw new InvalidSegmentException();
        }

        $class  = $this->getCustomNodeClass($conf);

        return new $class($this, $parent, $name);
    }

    /**
     * Generate same node.
     *
     * @param string             $same
     * @param NodeInterface|null $parent
     *
     * @return NodeInterface
     */
    protected function generateSame(
        string $same,
        ?NodeInterface $parent
    ): NodeInterface{
        $node = new SameNode($this, $parent, null);

        $node->setSame($same);

        return $node;
    }

    /**
     * Generate any node.
     *
     * @param NodeInterface|null $parent
     *
     * @return NodeInterface
     */
    protected function generateAny(?NodeInterface $parent): NodeInterface{
        return new AnyNode($this, $parent, null);
    }

    /**
     * Generate digit node.
     *
     * @param NodeInterface|null $parent
     * @param string|null        $name
     *
     * @return NodeInterface
     */
    protected function generateDigit(
        ?NodeInterface $parent,
        ?string $name
    ): NodeInterface{
        return new DigitNode($this, $parent, $name);
    }

    /**
     * Generate regex node.
     *
     * @param string        $regex
     * @param NodeInterface $parent
     * @param string|null   $name
     *
     * @return NodeInterface
     */
    protected function generateRegex(
        string $regex,
        NodeInterface $parent,
        ?string $name
    ): NodeInterface{
        $node   = new RegexNode($this, $parent, $name);

        $node->setRegex($regex);

        return $node;
    }

    /**
     * Generate wildcard node.
     *
     * @param string        $wildcard
     * @param NodeInterface $parent
     * @param string|null   $name
     *
     * @return NodeInterface
     */
    protected function generateWildcard(
        string $wildcard,
        NodeInterface $parent,
        ?string $name
    ): NodeInterface{
        $node   = new WildcardNode($this, $parent, $name);

        $node->setWildcard($wildcard);

        return $node;
    }
}
