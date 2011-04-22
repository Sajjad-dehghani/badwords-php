<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Filter;

use Badword\Dictionary;

/**
 * Result contains result data from a Filter execution.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Result
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $matches;

    /**
     * Constructs a new Result.
     *
     * @param string $content The content that was filtered.
     * @param array $matches The matches found in the content suspected of being bad words.
     */
    public function __construct($content, array $matches)
    {
        $this->content = $content;
        $this->matches = $matches;
    }

    /**
     * Gets the content that was filtered.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Gets the content that was filtered with suspected bad words highlighted using <span>'s.
     *
     * @return string
     */
    public function getHighlightedContent()
    {
        $content = htmlentities($this->getContent());

        foreach($this->getMatches() as $match)
        {
            $content = preg_replace('/('.$match.')/iu', '<span class="badword">$1</span>', $content);
        }

        return $content;
    }

    /**
     * Gets the matches found in the content suspected of being bad words.
     *
     * @return array
     */
    public function getMatches()
    {
        $matches = array();

        foreach($this->matches as $dictionaryMatches)
        {
            $matches = array_merge($matches, $dictionaryMatches);
        }

        return array_values(array_unique($matches));
    }

    /**
     * Gets the matches for a specific Dictionary found in the content suspected of being bad words.
     *
     * @param Dictionary $dictionary
     * 
     * @return array
     */
    public function getDictionaryMatches(Dictionary $dictionary)
    {
        return isset($this->matches[$dictionary->getId()]) ? $this->matches[$dictionary->getId()] : array();
    }

    /**
     * Determines if the content is clean or not, a.k.a. has any matches.
     *
     * @return boolean
     */
    public function isClean()
    {
        return count($this->getMatches()) === 0;
    }
}