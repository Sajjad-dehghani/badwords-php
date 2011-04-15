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

/**
 * Character defines the Rule for a specific character of a Word.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Character implements Rule
{
    /**
     * @var array
     */
    protected $alternativeCharacters;
    
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
        $this->setCharacter($character);
        $this->setAlternativeCharacters($alternativeCharacters);
        $this->setDetectRepetition($detectRepetition);
        $this->setCanBeRepeatedFor($canBeRepeatedFor);
    }

    /**
     * Adds an alternative character that can be present instead of the character, e.g. @ for a.
     *
     * @param string $alternativeCharacter
     * 
     * @return Character
     */
    public function addAlternativeCharacter($alternativeCharacter)
    {
        if (!$this->validateAlternativeCharacter($character))
        {
            throw new \InvalidArgumentException(sprintf('Invalid alternative character "%s". Please provide a single string character.', $alternativeCharacter));
        }

        if (!in_array($alternativeCharacter, $this->alternativeCharacters))
        {
            array_push($this->alternativeCharacters, $this->cleanAlternativeCharacter($alternativeCharacter));
        }

        return $this;
    }

    /**
     * Adds alternative characters that can be present instead of the character, e.g. @ for a.
     *
     * @param array $alternativeCharacters
     *
     * @return Character
     */
    public function addAlternativeCharacters(array $alternativeCharacters)
    {
        foreach($alternativeCharacters as $key => $alternativeCharacter)
        {
            if (!$this->validateAlternativeCharacter($alternativeCharacter))
            {
                throw new \InvalidArgumentException(sprintf('Invalid alternative character "%s". Please provide a single string character.', $alternativeCharacter));
            }

            $alternativeCharacters[$key] = $this->cleanAlternativeCharacter($alternativeCharacter);
        }

        $this->alternativeCharacters = array_unique(array_merge($this->alternativeCharacters, array_values($alternativeCharacters)));
        return $this;
    }

    /**
     * Gets the alternative characters that can be present instead of the character, e.g. @ for a.
     *
     * @return array
     */
    public function getAlternativeCharacters()
    {
        return $this->alternativeCharacters;
    }

    /**
     * Sets the alternative characters that can be present instead of the character, e.g. @ for a.
     *
     * @param array $alternativeCharacters
     *
     * @return Character
     */
    public function setAlternativeCharacters(array $alternativeCharacters)
    {
        foreach($alternativeCharacters as $key => $alternativeCharacter)
        {
            if (!$this->validateAlternativeCharacter($alternativeCharacter))
            {
                throw new \InvalidArgumentException(sprintf('Invalid alternative character "%s". Please provide a single string character.', $alternativeCharacter));
            }

            $alternativeCharacters[$key] = $this->cleanAlternativeCharacter($alternativeCharacter);
        }

        $this->alternativeCharacters = array_unique(array_values($alternativeCharacters));
        return $this;
    }

    /**
     * Validates an alternative character.
     *
     * @param string $alternativeCharacter
     *
     * @return boolean
     */
    protected function validateAlternativeCharacter($alternativeCharacter)
    {
        return is_string($alternativeCharacter) && mb_strlen(trim($alternativeCharacter)) === 1;
    }

    /**
     * Cleans an alternative character into the correct format.
     *
     * @param string $alternativeCharacter
     *
     * @return string
     */
    protected function cleanAlternativeCharacter($alternativeCharacter)
    {
        return mb_strtolower(trim($alternativeCharacter));
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
        if (!(mb_strlen($character) === 1))
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
        if (!($canBeRepeatedFor === null || ((is_int($canBeRepeatedFor) || ctype_digit($canBeRepeatedFor)) && $canBeRepeatedFor > 0)))
        {
            throw new \InvalidArgumentException(sprintf('Invalid can be repeated for value "%s". Please provide an integer greater than 0 or null.', $canBeRepeatedFor));
        }

        $this->canBeRepeatedFor = $canBeRepeatedFor !== null ? (int) $canBeRepeatedFor : null;
        return null;
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
        if (!is_bool($detectRepetition))
        {
            throw new \InvalidArgumentException(sprintf('Invalid detect repetition value "%s". Please provide a boolean.', $detectRepetition));
        }

        $this->detectRepetition = $detectRepetition;
        return $this;
    }
}