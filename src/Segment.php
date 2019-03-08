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

/**
 *
 */
class Segment{

    private const SEGMENT_REGEX = "/\A([A-Z_][0-9A-Z_]*)(?:(@|:)([^[:cntrl:]]+))?\z/i";

    /**
     * @var string
     */
    private $definition;

    /**
     * @var string|null
     */
    private $same;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $regex;

    /**
     * @var string|null
     */
    private $filter;

    /**
     * Constructor.
     *
     * @param   string  $segment
     */
    public function __construct(string $segment){
        $this->definition   = $segment;

        if("{" !== mb_substr($segment, 0, 1) || "}" !== mb_substr($segment, -1)){
            $this->same = rawurldecode($segment);

            return;
        }

        if(1 !== preg_match(self::SEGMENT_REGEX, mb_substr($segment, 1, -1), $m)){
            throw new InvalidSegmentException("'{$segment}' is invalid segment.");
        }

        $this->name = $m[1];

        if(isset($m[2])){
            if(":" === $m[2]){
                $this->regex    = $m[3];
            }else{
                $this->filter   = $m[3];
            }
        }
    }

    /**
     * Get definition.
     *
     * @return  string
     */
    public function getDefinition(): string{
        return $this->definition;
    }

    /**
     * Get same.
     *
     * Already url decoded.
     *
     * @return  string|null
     */
    public function getSame(): ?string{
        return $this->same;
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
     * Get regex.
     *
     * @return  string|null
     */
    public function getRegex(): ?string{
        return $this->regex;
    }

    /**
     * Get filter.
     *
     * @return  string|null
     */
    public function getFilter(): ?string{
        return $this->filter;
    }
}