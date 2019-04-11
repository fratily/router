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
namespace Fratily\Router\Node\Custom;

use Fratily\Router\Node\AbstractNode;

/**
 *
 */
class DigitNode extends AbstractNode{

    /**
     * @var int
     */
    private $min    = 0;

    /**
     * @var int
     */
    private $max    = PHP_INT_MAX;

    /**
     * Get min range.
     *
     * @return int
     */
    public function getMin(): int{
        return $this->min;
    }

    /**
     * Set min range.
     *
     * @param int $min
     *
     * @return void
     */
    public function setMin(int $min): void{
        if($this->max < $min){
            throw new \InvalidArgumentException();
        }

        $this->min  = $min;
    }

    /**
     * Get max range.
     *
     * @return int
     */
    public function getMax(): int{
        return $this->max;
    }

    /**
     * Set min range.
     *
     * @param int $max
     *
     * @return void
     */
    public function setMax(int $max): void{
        if($this->min > $max){
            throw new \InvalidArgumentException();
        }

        $this->max  = $max;
    }

    /**
     * {@inheritDoc}
     */
    public function isMatch(string $requestSegment): bool{
        return false !== filter_var(
            $requestSegment,
            FILTER_VALIDATE_INT,
            [
                "options"   => [
                    "min_range" => $this->getMin(),
                    "max_range" => $this->getMax(),
                ],
            ]
        );
    }
}