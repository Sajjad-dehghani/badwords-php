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

/**
 * Csv loads and formats a list of bad words from a CSV file.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Csv extends AbstractIndex
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Constucts a new Index.
     *
     * @param string $path The path to the CSV file.
     * @param Cache $cache The caching mechanism to use.
     */
    public function __construct($path, Cache $cache = null)
    {
        parent::__construct($cache);

        $this->setPath($path);
    }

    /**
     * Gets the path to the CSV file.
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path to the CSV file.
     *
     * @param string $path
     * 
     * @return Csv
     *
     * @throws \InvalidArgumentException When the path is invalid.
     */
    public function setPath($path)
    {
        if (!(is_string($path) && mb_strlen(trim($path)) > 0))
        {
            throw new \InvalidArgumentException('Invalid path. Please provide a path to a valid CSV file.');
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
        return 'csv_'.md5($this->getPath());
    }

    /**
     * Loads the list of words from the CSV file.
     *
     * @return array
     */
    protected function loadWordsFromSource()
    {
        $handle = fopen($this->getPath(), 'r');
        if ($handle === false)
        {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('CSV file not could be opened.');
            // @codeCoverageIgnoreEnd
        }

        $row = 0;
        $words = array();

        while(($data = fgetcsv($handle, 1024, ',')) !== false)
        {
            $row++;

            try
            {
                $this->validateRowData($data);
            }
            catch(\RuntimeException $e)
            {
                throw new \RuntimeException(sprintf('Invalid data detected in CSV file on row %s. %s', $row, $e->getMessage()));
            }

            array_push($words, $data);
        }

        return $words ?: null;
    }
    
    /**
     * Validates row data from the CSV file
     * 
     * @param array $data
     * 
     * @return boolean
     *
     * @throws \RuntimeException When an error is detected in the data.
     */
    protected function validateRowData(array $data)
    {
        $data = array_values($data);

        if (!(isset($data[0]) && is_string($data[0]) && mb_strlen(trim($data[0])) > 0))
        {
            throw new \RuntimeException('Column 1 must be a valid word.');
        }

        $allowedBooleanValues = array(true, false, 1, 0, '1', '0');

        if (isset($data[1]) && !in_array($data[1], $allowedBooleanValues, true))
        {
            throw new \RuntimeException('Column 2 must be a valid boolean, e.g. either 1 or 0, or omitted.');
        }

        if (isset($data[2]) && !in_array($data[2], $allowedBooleanValues, true))
        {
            throw new \RuntimeException('Column 3 must be a valid boolean, e.g. either 1 or 0, or omitted.');
        }

        return true;
    }
}