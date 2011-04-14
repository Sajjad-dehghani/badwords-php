<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword;

/**
 * Word represents a single "bad" word and its settings.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Word
{
    /**
     * @var boolean
     */
    protected $mustEndWord;
    
    /**
     * @var boolean
     */
    protected $mustStartWord;
    
    /**
     * @var string
     */
    protected $word;

    /**
     * Constructs a new Word.
     * 
     * @param string $word The "bad" word.
     * @param boolean $mustStartWord Whether the "bad" word must start a word.
     * @param boolean $mustEndWord Whether the "bad" word must end a word.
     */
    public function __construct($word, $mustStartWord = false, $mustEndWord = false)
    {
        $this->setWord($word);
        $this->setMustStartWord($mustStartWord);
        $this->setMustEndWord($mustEndWord);
    }

    /**
     * Gets whether the "bad" word must end a word.
     * 
     * @return boolean
     */
    public function getMustEndWord()
    {
        return $this->mustEndWord;
    }

    /**
     * Sets whether the "bad" word must end a word.
     *
     * @param boolean $mustEndWord
     *
     * @return Word
     */
    public function setMustEndWord($mustEndWord)
    {
        if(!(is_bool($mustEndWord)))
        {
            throw new \InvalidArgumentException('Invalid must end word. Please provide a boolean.');
        }

        $this->mustEndWord = $mustEndWord;
        return $this;
    }

    /**
     * Gets whether the "bad" word must start a word.
     *
     * @return boolean
     */
    public function getMustStartWord()
    {
        return $this->mustStartWord;
    }

    /**
     * Sets whether the "bad" word must start a word.
     *
     * @param boolean $mustEndWord
     *
     * @return Word
     */
    public function setMustStartWord($mustStartWord)
    {
        if(!(is_bool($mustStartWord)))
        {
            throw new \InvalidArgumentException('Invalid must start word. Please provide a boolean.');
        }

        $this->mustStartWord = $mustStartWord;
        return $this;
    }

    /**
     * Gets the "bad" word.
     *
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Sets the "bad" word.
     *
     * @param string $word
     *
     * @return Word
     */
    public function setWord($word)
    {
        if(!(is_string($word) && strlen(trim($word)) > 0))
        {
            throw new \InvalidArgumentException('Invalid word. Please provide a non-empty string.');
        }

        $this->word = trim($word);
        return $this;
    }
}