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
}