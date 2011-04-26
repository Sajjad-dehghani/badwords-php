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
use Badword\Filter\Result;

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
    protected $regExps = array();

    /**
     * Constructs a new Filter.
     * 
     * @param Dictionary|array $dictionaries The Dictionary or Dictionaries of bad words to filter against.
     * @param Config $config The Config used during execution.
     * @param Cache $cache The caching mechanism to use.
     */
    public function __construct($dictionaries, Config $config, Cache $cache = null)
    {
        $this->setDictionaries(!is_array($dictionaries) ? array($dictionaries) : $dictionaries);
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
     * Clears the local cache of regular expressions.
     *
     * @return Filter
     */
    protected function clearRegExps()
    {
        $this->regExps = array();
        return $this;
    }

    /**
     * Filters some content for bad words and returns a Result. 
     * If multiple contents are specified, multiple Results will be returned.
     *
     * @param string|array $content A single content string or an array of content strings.
     *
     * @return Result|array A single Result or an array of Results.
     *
     * @throws \InvalidArgumentException When a content string is invalid.
     */
    public function filter($content)
    {
        $singleContent = false;
        if(!is_array($content))
        {
            $content = array($content);
            $singleContent = true;
        }

        foreach($content as $key => $string)
        {
            if(!(is_string($string) && strlen(trim($string)) > 0))
            {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid content%s. Please provide a non-empty string.',
                    (count($string) > 1 ? sprintf(' at index "%s".', $key) : null)
                ));
            }
        }

        $results = array();
        $riskLevels = $this->getDictionaryRiskLevels();

        foreach($content as $key => $string)
        {
            array_push($results, new Result($string, $this->filterString($string), $riskLevels));
        }

        return count($results) === 1 && $singleContent ? $results[0] : $results;
    }

    /**
     * Filters a single string for bad words and returns any suspected matches found.
     *
     * @param string $string
     *
     * @return array
     */
    protected function filterString($string)
    {
        $matches = array();

        // For each Dictionary
        foreach($this->getDictionaries() as $dictionary)
        {
            $dictionaryMatches = array();

            // Get the regular expressions
            $regExps = $this->getDictionaryRegExps($dictionary);

            // Run each one on the string
            foreach($regExps as $regExp)
            {
                // If matches are found
                if(preg_match_all('/'.$regExp.'/iu', $string, $regExpMatches))
                {
                    // If there are whitelisted words
                    if($this->getConfig()->getWhitelistedWords())
                    {
                        // Only store each match if it isn't in the whitelist
                        foreach($regExpMatches[0] as $regExpMatch)
                        {
                            if(!in_array(mb_strtolower(trim($regExpMatch)), $this->getConfig()->getWhitelistedWords()))
                            {
                                array_push($dictionaryMatches, $regExpMatch);
                            }
                        }
                    }
                    // Otherwise just straight store them
                    else
                    {
                        $dictionaryMatches = array_merge($dictionaryMatches, $regExpMatches[0]);
                    }
                }
            }

            // If matches were found, store them against the Dictionary ID
            if($dictionaryMatches)
            {
                $matches[$dictionary->getId()] = array_values(array_unique($dictionaryMatches));
            }
        }

        return $matches;
    }

    /**
     * Gets the regular expressions for the Dictionary.
     *
     * @param Dictionary $dictionary
     * 
     * @return array
     */
    protected function getDictionaryRegExps(Dictionary $dictionary)
    {
        if(!isset($this->regExps[$dictionary->getId()]))
        {
            $this->regExps[$dictionary->getId()] = $this->loadOrGenerateDictionaryRegExps($dictionary);
        }

        return $this->regExps[$dictionary->getId()];
    }

    /**
     * Loads the the Dictionary regular expressions from the cache or generates them.
     * 
     * @param Dictionary $dictionary
     * 
     * @return array
     */
    protected function loadOrGenerateDictionaryRegExps(Dictionary $dictionary)
    {
        $fromCache = true;
        $regExps = $this->loadDictionaryRegExpsFromCache($dictionary);
        if(!$regExps)
        {
            $fromCache = false;
            $regExps = $this->generateDictionaryRegExps($dictionary);
        }

        if(!(is_array($regExps)))
        {
            throw new Exception(sprintf('Error while loading or generating regular expressions for Dictionary with ID "%s".', $dictionary->getId()));
        }

        if(!$fromCache)
        {
            $this->saveDictionaryRegExpsToCache($dictionary, $regExps);
        }

        return $regExps;
    }

    /**
     * Loads the Dictionary regular expressions from the cache.
     *
     * @param Dictionary $dictionary
     *
     * @return array
     */
    protected function loadDictionaryRegExpsFromCache(Dictionary $dictionary)
    {
        $cache = $this->getCache();
        $cacheKey = $this->getDictionaryRegExpsCacheKey($dictionary);
        return $cache->has($cacheKey) ? $cache->get($cacheKey) : null;
    }

    /**
     * Generates the regular expressions for a Dictionary using the set Config.
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
            array_push($wordRegExps, $this->getConfig()->applyRulesToWord($word));
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

            // Store
            array_push($regExps[$index], $wordRegExp);
        }

        // Concatenate the Word regular expressions
        foreach($regExps as $key => $wordRegExps)
        {
            $regExps[$key] = implode('|', $wordRegExps);
        }

        return $regExps;
    }

    /**
     * Saves the Dictionary regular expressions to the cache.
     *
     * @param Dictionary $dictionary
     * @param array $regExps
     *
     * @return boolean
     */
    protected function saveDictionaryRegExpsToCache(Dictionary $dictionary, array $regExps)
    {
        return $this->getCache()->set($this->getDictionaryRegExpsCacheKey($dictionary), $regExps);
    }

    /**
     * Gets the key used to read/store the Dictionary regular expressions from the cache.
     *
     * @param Dictionary $dictionary
     *
     * @return string
     */
    protected function getDictionaryRegExpsCacheKey(Dictionary $dictionary)
    {
        return $dictionary->getId().'_regexps_'.(md5(serialize($this->getConfig())));
    }

    /**
     * Gets the risk level of each Dictionary indexed by Dictionary ID.
     *
     * @return array
     */
    protected function getDictionaryRiskLevels()
    {
        $riskLevels = array();

        foreach($this->getDictionaries() as $dictionary)
        {
            $riskLevels[$dictionary->getId()] = $dictionary->getRiskLevel();
        }

        return $riskLevels;
    }
}