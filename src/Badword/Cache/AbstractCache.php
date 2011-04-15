<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Cache;

use Badword\Cache;

/**
 * AbstractCache is the base cache class for all Cache classes.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
abstract class AbstractCache implements Cache
{
    /**
     * @var integer
     */
    protected $defaultLifetime;
    
    /**
     * @var string
     */
    protected $prefix;

    /**
     * Constructs a new cache.
     *
     * @param string $prefix The text to prefix to each cache entry.
     * @param integer $defaultLifetime The default amount of time the data should be stored.
     */
    public function __construct($prefix = 'badword_', $defaultLifetime = null)
    {
        $this->setPrefix($prefix);
        $this->setDefaultLifetime($defaultLifetime);
    }

    /**
     * Gets the default cache lifetime.
     *
     * @return integer
     */
    public function getDefaultLifetime()
    {
        return $this->defaultLifetime;
    }

    /**
     * Sets the default cache lifetime.
     *
     * @param integer $defaultLifetime
     *
     * @return AbstractCache
     */
    public function setDefaultLifetime($defaultLifetime = null)
    {
        if(!$this->validateLifetime($defaultLifetime))
        {
            throw new \InvalidArgumentException(sprintf('Invalid default lifetime "%s". Expected integer greater than 0 or null.', $defaultLifetime));
        }
        
        $this->defaultLifetime = $defaultLifetime !== null ? (int) $defaultLifetime : null;
        return $this;
    }
    
    /**
     * Validates a lifetime value.
     *
     * @param integer $lifetime
     *
     * @return boolean
     */
    protected function validateLifetime($lifetime)
    {
        return $lifetime === null || ((is_int($lifetime) || ctype_digit($lifetime)) && $lifetime > 0);
    }

    /**
     * Gets the cache prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Sets the cache prefix.
     *
     * @param string $prefix
     *
     * @return AbstractCache
     */
    public function setPrefix($prefix)
    {
        if(!(is_string($prefix) && mb_strlen(trim($prefix)) > 0))
        {
            throw new \InvalidArgumentException(sprintf('Invalid prefix "%s". Please provide a non-empty string.', $prefix));
        }

        $this->prefix = $prefix;
        return $this;
    }
}