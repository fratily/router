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
namespace Fratily\Router\Node;

/**
 *
 */
class SameNode extends AbstractNode{

    /**
     * @var string
     */
    private $same   = "";

    /**
     * Get same string.
     *
     * @return string
     */
    public function getSame(): string{
        return $this->same;
    }

    /**
     * Set same string.
     *
     * @param string $same
     *
     * @return void
     */
    public function setSame(string $same): void{
        $this->same = $same;
    }

    /**
     * {@inheritDoc}
     */
    public function isMatch(string $requestSegment): bool{
        return $this->same === $requestSegment;
    }

    /**
     * {@inheritDoc}
     */
    public function getSegment($parameter): string{
        return $this->getSame();
    }
}