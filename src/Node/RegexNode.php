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
class RegexNode extends AbstractNode{

    protected const DEFAULT_REGEX       = ".+?";

    protected const DEFAULT_DELIMITER   = "/";

    protected const DEFAULT_MODIFIER    = "u";

    protected const BRACKET_DELIMITERS    = [
        "(" => ")",
        "{" => "}",
        "[" => "]",
        "<" => ">",
    ];

    /**
     * @var string|null
     */
    private $regex;

    /**
     * @var string|null
     */
    private $delimiter;

    /**
     * @var string|null
     */
    private $modifier;

    /**
     * Get regex.
     *
     * @return string
     */
    public function getRegex(): string{
        return $this->regex ?? static::DEFAULT_REGEX;
    }

    /**
     * Set regex.
     *
     * @param string
     *
     * @return void
     */
    public function setRegex(string $regex): void{
        $this->regex    = preg_quote($regex, static::DELIMITERS);
    }

    /**
     * Get delimiter.
     *
     * @return string
     */
    public function getDelimiter(): string{
        return $this->delimiter ?? static::DEFAULT_DELIMITER;
    }

    /**
     * Get end delimiter.
     *
     * @return string
     */
    public function getEndDelimiter(): string{
        $delimiter  = $this->getDelimiter();

        if(isset(self::BRACKET_DELIMITERS[$delimiter])){
            return self::BRACKET_DELIMITERS[$delimiter];
        }

        return $delimiter;
    }

    /**
     * Set delimiter.
     *
     * @param string $delimiter
     *
     * @return void
     */
    public function setDelimiter(string $delimiter): void{
        if(1 !== strlen($delimiter)){
            throw new \InvalidArgumentException();
        }

        $char = ord($delimiter);

        if(33 > $char || 126 < $char){
            throw new \InvalidArgumentException();
        }

        if(
            (47 < $char && 58 > $char)
            || (64 < $char && 91 > $char)
            || (96 < $char && 123 > $char)
        ){
            throw new \InvalidArgumentException();
        }

        $this->delimiter    = $delimiter;
    }

    /**
     * Get modifier.
     *
     * @return string
     */
    public function getModifier(): string{
        return $this->modifier ?? static::DEFAULT_MODIFIER;
    }

    /**
     * Set modifier.
     *
     * @param string $modifier
     *
     * @return void
     */
    public function setModifier(string $modifier): void{
        if(1 !== preg_match("/\A[imsxADSUXJu]+\z/", $modifier)){
            throw new \InvalidArgumentException();
        }

        $this->modifier = $modifier;
    }

    /**
     * {@inheritDoc}
     */
    public function isMatch(string $requestSegment): bool{
        return 1 === preg_match($this->generateRegex(), $requestSegment);
    }

    /**
     * Generate full regex.
     *
     * @return string
     */
    protected function generateRegex(): string{
        $delimiter      = $this->getDelimiter();
        $endDelimiter   = $this->getEndDelimiter();

        return $delimiter . "\A{$this->getRegex()}\z" . $endDelimiter . $this->getModifier();
    }
}