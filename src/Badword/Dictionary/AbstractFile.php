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

/**
 * AbstractFile is the base class for all Dictionaries
 * that use a file as their source.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
abstract class AbstractFile extends AbstractDictionary
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Constucts a new Dictionary.
     *
     * @param string $path The path to the source file.
     * @param Cache $cache The caching mechanism to use.
     */
    public function __construct($path, Cache $cache = null)
    {
        parent::__construct($cache);

        $this->setPath($path);
    }

    /**
     * Gets the path to the source file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path to the source file.
     *
     * @param string $path
     *
     * @return AbstractFile
     *
     * @throws \InvalidArgumentException When the path is invalid.
     */
    public function setPath($path)
    {
        if (!(is_string($path) && mb_strlen(trim($path)) > 0))
        {
            throw new \InvalidArgumentException('Invalid path. Please provide a path to a valid source file.');
        }

        $path = trim($path);

        if (!(is_readable($path) && !is_dir($path)))
        {
            throw new \InvalidArgumentException('Invalid path. The specified path is either invalid, can not be found, or can not be read.');
        }

        $this->path = realpath($path);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getFileType().'_'.md5($this->getPath());
    }

    /**
     * Gets the type of source file this Dictionary uses.
     *
     * @return string
     */
    abstract protected function getFileType();
}