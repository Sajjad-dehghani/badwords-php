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

/**
 * Csv loads and formats a list of bad words from a CSV file.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Csv extends AbstractFile
{
    /**
     * {@inheritdoc}
     */
    protected function getFileType()
    {
        return 'csv';
    }

    /**
     * Loads the words data from the source CSV file.
     *
     * @return array
     */
    protected function loadWordsDataFromSource()
    {
        $handle = fopen($this->getPath(), 'r');
        if ($handle === false)
        {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('CSV file not could be opened.');
            // @codeCoverageIgnoreEnd
        }

        $row = 0;
        $wordsData = array();

        while(($rowData = fgetcsv($handle, 1024, ',')) !== false)
        {
            $row++;

            try
            {
                $rowData = $this->validateAndCleanWordData($rowData);
            }
            catch(\RuntimeException $e)
            {
                throw new \RuntimeException(sprintf('Invalid word data detected in CSV file on row %s. %s', $row, $e->getMessage()));
            }

            array_push($wordsData, $rowData);
        }

        return $wordsData ?: null;
    }
    
    /**
     * Validates and cleans the word data from the CSV file.
     * 
     * @param array $wordData
     * 
     * @return boolean
     *
     * @throws \RuntimeException When an error is detected in the word data.
     */
    protected function validateAndCleanWordData(array $wordData)
    {
        $wordData = array_values($wordData);

        if (!(isset($wordData[0]) && is_string($wordData[0]) && mb_strlen(trim($wordData[0])) > 0))
        {
            throw new \RuntimeException('Column 1 must be a valid word.');
        }

        $allowedBooleanValues = array(true, false, 1, 0, '1', '0');

        if (isset($wordData[1]))
        {
            if (!in_array($wordData[1], $allowedBooleanValues, true))
            {
                throw new \RuntimeException('Column 2 must be a valid boolean, e.g. either 1 or 0, or omitted.');
            }
            else
            {
                $wordData[1] = (bool) $wordData[1];
            }
        }

        if (isset($wordData[2]))
        {
            if (!in_array($wordData[2], $allowedBooleanValues, true))
            {
                throw new \RuntimeException('Column 3 must be a valid boolean, e.g. either 1 or 0, or omitted.');
            }
            else
            {
                $wordData[2] = (bool) $wordData[2];
            }
        }

        return $wordData;
    }
}