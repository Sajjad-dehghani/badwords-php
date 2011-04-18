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

use Badword\Cache;
use Badword\Cache\None;
use Badword\Filter\Config;

/**
 * Filter detects bad words in content.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Filter
{
    const REGEXP_MAX_LENGTH = 3000;

    /**
     * @var Cache
     */
    protected $cache;
    
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $dictionaries = array();

    /**
     * @var array
     */
    protected $regExps;

    /**
     * Constructs a new Filter.
     * 
     * @param array $dictionaries The Dictionaries of bad words to filter against.
     * @param Config $config The Config used during execution.
     * @param Cache $cache The caching mechanism to use.
     */
    public function __construct(array $dictionaries, Config $config, Cache $cache = null)
    {
        $this->setDictionaries($dictionaries);
        $this->setConfig($config);
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
     * @return Filter
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Gets the Config used during execution.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the Config used during execution.
     *
     * @param Config $config
     * 
     * @return Filter
     */
    public function setConfig(Config $config)
    {
        if($config !== $this->getConfig())
        {
            $this->clearRegExps();
        }

        $this->config = $config;
        return $this;
    }

    /**
     * Adds a Dictionary of bad words to filter against.
     *
     * @param Dictionary $dictionary
     *
     * @return Filter
     */
    public function addDictionary(Dictionary $dictionary)
    {
        if(!in_array($dictionary, $this->getDictionaries()))
        {
            array_push($this->dictionaries, $dictionary);
            
            $this->clearRegExps();
        }

        return $this;
    }

    /**
     * Adds Dictionaries of bad words to filter against.
     *
     * @param array $dictionaries
     *
     * @return Filter
     *
     * @throws \InvalidArgumentException When a dictionary is invalid.
     */
    public function addDictionaries(array $dictionaries)
    {
        foreach($dictionaries as $key => $dictionary)
        {
            if(!($dictionary instanceof Dictionary))
            {
                throw new \InvalidArgumentException(sprintf('Invalid dictionary at key "%s". Expected instance of \Badword\Dictionary.', $key));
            }
        }

        foreach($dictionaries as $dictionary)
        {
            $this->addDictionary($dictionary);
        }

        return $this;
    }

    /**
     * Gets the Dictionaries of bad words to filter against.
     *
     * @return array
     */
    public function getDictionaries()
    {
        return $this->dictionaries;
    }

    /**
     * Sets the Dictionaries of bad words to filter against.
     *
     * @param array $dictionaries
     *
     * @return Filter
     *
     * @throws \InvalidArgumentException When a dictionary is invalid.
     */
    public function setDictionaries(array $dictionaries)
    {
        foreach($dictionaries as $dictionary)
        {
            if(!($dictionary instanceof Dictionary))
            {
                throw new \InvalidArgumentException(sprintf('Invalid dictionary at key "%s". Expected instance of \Badword\Dictionary.', $key));
            }
        }

        $this->dictionaries = array();

        foreach($dictionaries as $dictionary)
        {
            $this->addDictionary($dictionary);
        }

        return $this;
    }

    /**
     * Gets the regular expressions for the Dictionaries.
     * 
     * @return array
     */
    public function getRegExps()
    {
        if($this->regExps === null)
        {
            $this->regExps = $this->generateRegExps();
        }

        return $this->regExps;
    }

    /**
     * Clears the local cache of regular expressions.
     * 
     * @return Filter 
     */
    protected function clearRegExps()
    {
        $this->regExps = null;
        return $this;
    }

    /**
     * Generates the regular expressions for the Dictionaries using the Config.
     *
     * @return array
     */
    protected function generateRegExps()
    {
        $regExps = array();

        foreach($this->getDictionaries() as $dictionary)
        {
            $regExps[$dictionary->getId()] = $this->generateDictionaryRegExps($dictionary);
        }

        return $regExps;
    }

    /**
     * Generates the regular expressions for a Dictionary using the Config.
     *
     * @param Dictionary $dictionary
     *
     * @return array
     */
    protected function generateDictionaryRegExps(Dictionary $dictionary)
    {
        // Convert each Word in the Dictionary to a regular expressions
        $wordRegExps = array();
        foreach($dictionary->getWords() as $word)
        {
            array_push($wordRegExps, $this->getConfig()->apply($word));
        }

        $regExps = array();
        $totalLength = 0;

        // Group the regular expressions to be concatenated with a maximum
        // length of REGEXP_MAX_LENGTH for each concatenation
        foreach($wordRegExps as $wordRegExp)
        {
            $wordRegExp = '('.$wordRegExp.')';

            $totalLength += mb_strlen($wordRegExp);

            $index = ceil($totalLength / self::REGEXP_MAX_LENGTH) - 1;
            if(!isset($regExps[$index]))
            {
                $regExps[$index] = array();
            }

            // Stor
            array_push($regExps[$index], $wordRegExp);
        }

        // Concatenate the Word regular expressions
        foreach($regExps as $key => $wordRegExps)
        {
            $regExps[$key] = implode('|', $wordRegExps);
        }

        return $regExps;
    }
}