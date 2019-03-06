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
    private $comparator;

    /**
     * @var string|null
     */
    private $same;

    /**
     * Constructor.
     *
     * @param   string  $segment
     */
    public function __construct(string $segment){
        $atSignPos  = mb_strpos($segment, "@");

        if(false !== $atSignPos){
            if(
                0 !== $atSignPos
                && "\\" === mb_substr($segment, $atSignPos - 1, $atSignPos)
            ){
                // At sign escaped.
                $segment    = mb_substr($segment, 0, $atSignPos - 1)
                    . mb_substr($segment, $atSignPos)
                ;
            }else{
                $this->comparator   = mb_substr($segment, $atSignPos + 1);
                $segment            = mb_substr($segment, 0, $atSignPos);
            }
        }

        if(":" === mb_substr($segment, 0, 1)){
            $this->name = mb_substr($segment, 1);
        }else{
            if("\\:" === mb_substr($segment, 0, 2)){
                $segment    = mb_substr($segment, 1);
            }

            $this->same = $segment;

            if(null !== $this->comparator){
                if("" === $this->same){
                    $this->same = null;
                }else{
                    $this->same         = "{$this->same}@{$this->comparator}";
                    $this->comparator   = null;
                }
            }
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
     * Get comparator name.
     *
     * @return  string|null
     */
    public function getComparator(): ?string{
        return $this->comparator;
    }

    /**
     * Get same.
     *
     * @return  string|null
     */
    public function getSame(): ?string{
        return $this->same;
    }
}