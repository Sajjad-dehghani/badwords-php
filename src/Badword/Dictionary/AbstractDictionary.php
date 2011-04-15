<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Dictionary;

use Badword\Cache;
use Badword\Cache\None;
use Badword\Dictionary;
use Badword\Word;

/**
 * AbstractDictionary is the base class for all Dictionary classes.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
abstract class AbstractDictionary implements Dictionary
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var boolean
     */
    protected $mustEndWordDefault = false;

    /**
     * @var boolean
     */
    protected $mustStartWordDefault = false;

    /**
     * @var array
     */
    protected $words;

    /**
     * Constucts a new Dictionary.
     * 
     * @param Cache $cache The caching mechanism to use.
     */
    public function __construct(Cache $cache = null)
    {
        $this->setCache($cache ?: new None());
    }

    /**
     * Gets the caching mechanism.
     *
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Sets the caching mechanism.
     * 
     * @param Cache $cache
     * 
     * @return AbstractDictionary
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Gets the "must end word" default.
     *
     * @return boolean
     */
    public function getMustEndWordDefault()
    {
        return $this->mustEndWordDefault;
    }

    /**
     * Sets the "must end word" default.
     *
     * @param boolean $mustEndWordDefault
     *
     * @return AbstractDictionary
     */
    public function setMustEndWordDefault($mustEndWordDefault = false)
    {
        if (!is_bool($mustEndWordDefault))
        {
            throw new \InvalidArgumentException('Invalid "must end word" default. Must be a boolean.');
        }

        $this->mustEndWordDefault = $mustEndWordDefault;
        return $this;
    }

    /**
     * Gets the "must start word" default.
     *
     * @return boolean
     */
    public function getMustStartWordDefault()
    {
        return $this->mustStartWordDefault;
    }

    /**
     * Sets the "must start word" default.
     *
     * @param boolean $mustStartWordDefault
     *
     * @return AbstractDictionary
     */
    public function setMustStartWordDefault($mustStartWordDefault = false)
    {
        if (!is_bool($mustStartWordDefault))
        {
            throw new \InvalidArgumentException('Invalid "must start word" default. Must be a boolean.');
        }

        $this->mustStartWordDefault = $mustStartWordDefault;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWords()
    {
        if ($this->words === null)
        {
            $this->words = $this->loadWords();
        }

        return $this->words;
    }

    /**
     * Loads the Words either from the cache or directly from the source.
     *
     * @return array
     */
    protected function loadWords()
    {
        $fromCache = true;
        $wordsData = $this->loadWordsDataFromCache();
        if (!$wordsData)
        {
            $fromCache = false;
            $wordsData = $this->loadWordsDataFromSource();
        }

        if (!(is_array($wordsData) && count($wordsData) > 0))
        {
            throw new \RuntimeException('Words could not be loaded. Load failed or source was empty.');
        }

        if (!$fromCache)
        {
            $this->saveWordsDataToCache($wordsData);
        }

        $words = array();
        foreach($wordsData as $wordData)
        {
            array_push($words, $this->convertWordDataToObject($wordData));
        }

        return $words;
    }

    /**
     * Loads the words data from the cache.
     *
     * @return array
     */
    protected function loadWordsDataFromCache()
    {
        $cache = $this->getCache();
        $cacheKey = $this->getCacheKey();
        return $cache->has($cacheKey) ? $cache->get($cacheKey) : null;
    }

    /**
     * Loads the words data from the source.
     *
     * @return array
     */
    abstract protected function loadWordsDataFromSource();

    /**
     * Saves the words data to the cache.
     *
     * @param array $wordsData
     *
     * @return boolean
     */
    protected function saveWordsDataToCache(array $wordsData)
    {
        return $this->getCache()->set($this->getCacheKey(), $wordsData);
    }

    /**
     * Gets the key used to read/store the words data from the cache.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->getId().'_words_data';
    }

    /**
     * Converts a valid array of word data in a new Word object.
     *
     * @param array $wordData
     *
     * @return Word
     */
    protected function convertWordDataToObject(array $wordData)
    {
        return new Word(
            (string) $wordData[0],
            (isset($wordData[1]) ? (bool) $wordData[1] : $this->getMustStartWordDefault()),
            (isset($wordData[2]) ? (bool) $wordData[2] : $this->getMustEndWordDefault())
        );
    }
}