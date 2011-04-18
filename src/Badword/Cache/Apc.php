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

/**
 * Cache class that uses APC.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Apc extends AbstractCache
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException When APC can not be found or used.
     */
    public function __construct($prefix = self::DEFAULT_PREFIX, $defaultLifetime = null)
    {
        if(!function_exists('apc_store') || !ini_get('apc.enabled'))
        {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('You must have APC installed and enabled to use the Apc cache class.');
            // @codeCoverageIgnoreEnd
        }

        parent::__construct($prefix, $defaultLifetime);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $value = $this->fetch($key, $has);
        return $has ? $value : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if(function_exists('apc_exists'))
        {
            // @codeCoverageIgnoreStart
            return apc_exists($key);
            // @codeCoverageIgnoreEnd
        }
        else
        {
            // @codeCoverageIgnoreStart
            $this->fetch($key, $has);
            return $has;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Gets data from the APC cache.
     *
     * @param string $key Unique identifier for the cached data.
     * @param boolean $success Reference to a variable where fetch success/failure will be stored.
     *
     * @return mixed
     */
    protected function fetch($key, &$success)
    {
        $fetchSuccess = null;
        $value = apc_fetch($this->getPrefix().$key, $fetchSuccess);

        if($fetchSuccess !== null)
        {
            $success = $fetchSuccess;
        }
        else
        {
            // @codeCoverageIgnoreStart
            $success = $value !== false;
            // @codeCoverageIgnoreEnd
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException When the lifetime is invalid.
     */
    public function set($key, $data, $lifetime = null)
    {
        if(!$this->validateLifetime($lifetime))
        {
            throw new \InvalidArgumentException(sprintf('Invalid lifetime "%s". Expected integer greater than 0.', $lifetime));
        }
        else if($lifetime === null)
        {
            $lifetime = $this->getDefaultLifetime();
        }
        
        return apc_store($this->getPrefix().$key, $data, $lifetime);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return apc_delete($this->getPrefix().$key);
    }
}