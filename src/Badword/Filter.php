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
     * @var Dictionary
     */
    protected $dictionary;

    /**
     * Constructs a new Filter.
     * 
     * @param Dictionary $dictionary The Dictionary of bad words.
     * @param Config $config The Config used during execution.
     */
    public function __construct(Dictionary $dictionary, Config $config, Cache $cache = null)
    {
        $this->setDictionary($dictionary);
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
     * Gets the Dictionary of bad words.
     *
     * @return Dictionary
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }

    /**
     * Sets the Dictionary of bad words.
     *
     * @param Dictionary $dictionary
     * 
     * @return Filter
     */
    public function setDictionary(Dictionary $dictionary)
    {
        $this->dictionary = $dictionary;
        return $this;
    }
}