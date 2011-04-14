<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Index;

use Badword\Cache;
use Badword\Cache\None;

use Badword\Index;
use Badword\Word;

/**
 * AbstractIndex is the base class for all Index classes.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
abstract class AbstractIndex implements Index
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var array
     */
    protected $words;

    /**
     * Constucts a new Index.
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
     * @return AbstractIndex
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Converts an array of word data in a new Word object.
     *
     * @param array $data
     *
     * @return Word
     */
    protected function convertArrayToWord(array $data)
    {
        return new Word((string) $data[0], (bool) $data[1], (bool) $data[2]);
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
        $words = $this->loadWordsDataFromCache();
        if (!$words)
        {
            $words = $this->loadWordsDataFromSource();
        }

        if (!(is_array($words) && count($words) > 0))
        {
            throw new \RuntimeException('Words could not be loaded.');
        }

        $wordObjects = array();

        foreach($words as $word)
        {
            array_push($wordObjects, $this->convertArrayToWord($word));
        }

        return $wordObjects;
    }

    /**
     * Loads the list of words from the cache.
     *
     * @return array
     */
    protected function loadWordsDataFromCache()
    {
        return $this->getCache()->get($this->getId().'_words');
    }

    /**
     * Loads the list of words from the source.
     *
     * @return array
     */
    abstract protected function loadWordsDataFromSource();
}