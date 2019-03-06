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
namespace Fratily\Router\Segment;

/**
 *
 */
class Segment{

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $same;


    public function __construct(string $segment){
        if(":" === mb_substr($segment, 0, 1)){
            $this->name = mb_substr($segment, 1);
        }else{
            if("\\" === mb_substr($segment, 0, 1)){
                $segment    = mb_substr($segment, 1);
            }

            $this->same = $segment;
        }

    }

    /**
     * Get name.
     *
     * @return  string|null
     */
    public function getName(): ?string{
        return $this->name;
    }

    /**
     * With new name.
     *
     * @param   string|null $name
     *
     * @return  static
     */
    public function withName(?string $name): self{
        $clone          = clone $this;
        $clone->name    = $name;

        return $clone;
    }

    /**
     * Get same.
     *
     * @return  string|null
     */
    public function getSame(): ?string{
        return $this->same;
    }

    /**
     * With new define.
     *
     * @param   string|null $same
     *
     * @return  static
     */
    public function withSame(?string $same): self{
        $clone          = clone $this;
        $clone->same    = $same;

        return $clone;
    }
}