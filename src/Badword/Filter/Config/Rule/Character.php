<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Filter\Config\Rule;

use Badword\Filter\Config\Rule;
use Badword\Word;

/**
 * Character defines the rule for a specific character of a Word.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Character extends AbstractCharacter
{
    /**
     * @var string
     */
    protected $character;

    /**
     * @var integer
     */
    protected $canBeRepeatedFor;

    /**
     * @var boolean
     */
    protected $detectRepetition;

    /**
     * Constructs a new Character.
     * 
     * @param string $character The character this config applies to.
     * @param array $alternativeCharacters The alternative characters that can be present instead of the character, e.g. @ for a.
     * @param boolean $detectRepetition Whether character repetition should be detected or not, e.g. detect aaaaaaa for a.
     * @param integer $canBeRepeatedFor Whether the character can be repeated for X number of times, e.g. s can be repeated 2 times in some words, i.e. bass.
     */
    public function __construct($character, array $alternativeCharacters = array(), $detectRepetition = false, $canBeRepeatedFor = null)
    {
        parent::__construct($alternativeCharacters);

        $this->setCharacter($character);
        $this->setDetectRepetition($detectRepetition);
        $this->setCanBeRepeatedFor($canBeRepeatedFor);
    }

    /**
     * Gets the character this config applies to.
     *
     * @return string
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Sets the character this config applies to.
     *
     * @param string $character
     *
     * @return Character
     *
     * @throws \InvalidArgumentException When the character is invalid.
     */
    public function setCharacter($character)
    {
        if(!(is_string($character) && mb_strlen($character) === 1))
        {
            throw new \InvalidArgumentException(sprintf('Invalid character "%s". Please provide a single character string.', $character));
        }

        $this->character = mb_strtolower($character);
        return $this;
    }

    /**
     * Gets whether the character can be repeated for X number of times,
     * e.g. s can be repeated 2 times in some words, i.e. bass.
     *
     * @return integer
     */
    public function getCanBeRepeatedFor()
    {
        return $this->canBeRepeatedFor;
    }

    /**
     * Gets whether the character can be repeated for X number of times,
     * e.g. s can be repeated 2 times in some words, i.e. bass.
     *
     * @param integer $canBeRepeatedFor
     * 
     * @return Character
     */
    public function setCanBeRepeatedFor($canBeRepeatedFor = null)
    {
        if(!($canBeRepeatedFor === null || (is_int($canBeRepeatedFor) && $canBeRepeatedFor > 0)))
        {
            throw new \InvalidArgumentException(sprintf('Invalid can be repeated for value "%s". Please provide an integer greater than 0 or null.', $canBeRepeatedFor));
        }

        $this->canBeRepeatedFor = $canBeRepeatedFor ?: null;
        return $this;
    }

    /**
     * Gets whether character repetition should be detected or not, e.g. detect aaaaaaa for a.
     * 
     * @return boolean
     */
    public function getDetectRepetition()
    {
        return $this->detectRepetition;
    }

    /**
     * Sets whether character repetition should be detected or not, e.g. detect aaaaaaa for a.
     *
     * @param boolean $detectRepetition
     *
     * @return Character
     */
    public function setDetectRepetition($detectRepetition)
    {
        if(!is_bool($detectRepetition))
        {
            throw new \InvalidArgumentException(sprintf('Invalid detect repetition value "%s". Please provide a boolean.', $detectRepetition));
        }

        $this->detectRepetition = $detectRepetition;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($regExp, Word $word)
    {
        // If the letter can be repeated X number of times legally
        if($this->getCanBeRepeatedFor() !== null)
        {
            // Add repetition detection and set the minimum number required to X
            $regExp = preg_replace(
                sprintf('/%s{%s,}/iu', $this->getCharacter(), $this->getCanBeRepeatedFor()),
                sprintf('%s{%s,}', $this->getCharacter(), $this->getCanBeRepeatedFor()),
                $regExp
            );
        }

        // If we need to detect this letter being repeated
        if($this->detectRepetition)
        {
            // Add repetition detection
            $regExp = preg_replace(
                sprintf('/%s([^\{]|$)/iu', $this->getCharacter()),
                sprintf('%s+$1', $this->getCharacter()),
                $regExp
            );
        }

        // If there are alternative characters that could be used in place of this character
        if($this->getAlternativeCharacters())
        {
            // Add detection for them
            $alternativeCharacters = array_merge(array($this->getCharacter()), $this->getAlternativeCharacters());
            $alternativeCharacters = preg_replace('/(\*|\?|\$|\^)/iu', '\\\$1', implode('|', $alternativeCharacters));
            $regExp = preg_replace(sprintf('/%s/ui', $this->getCharacter()), '('.$alternativeCharacters.')', $regExp);
        }

        return $regExp;
    }
}