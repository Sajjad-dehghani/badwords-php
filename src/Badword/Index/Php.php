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

/**
 * Php loads and formats a list of bad words from a PHP file.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Php extends AbstractFile
{
    /**
     * {@inheritdoc}
     */
    protected function getFileType()
    {
        return 'php';
    }

    /**
     * Loads the words data from the PHP file.
     *
     * @return array
     */
    protected function loadWordsDataFromSource()
    {
        function includeFile($path)
        {
            ob_start();
            require($path);
            ob_end_clean();

            if (!(isset($words) && is_array($words)))
            {
                throw new \RuntimeException('"$words" variable could not be found or is not an array in the PHP file.');
            }

            return $words;
        };

        $data = includeFile($this->getPath());
        $wordsData = array();
        
        foreach($data as $key => $wordData)
        {
            try
            {
                $wordData = $this->validateAndCleanWordData($wordData);
            }
            catch(\RuntimeException $e)
            {
                throw new \RuntimeException(sprintf('Invalid word data detected in PHP file at key "%s". %s', $key, $e->getMessage()));
            }

            array_push($wordsData, $wordData);
        }

        return $wordsData;
    }
    
    /**
     * Validates and cleans the word data from the PHP file.
     * 
     * @param array $wordData
     * 
     * @return boolean
     *
     * @throws \RuntimeException When an error is detected in the word data.
     */
    protected function validateAndCleanWordData($wordData)
    {
        if (is_string($wordData))
        {
            $wordData = array($wordData);
        }

        if (!is_array($wordData))
        {
            throw new \RuntimeException('Data must be an array or string.');
        }

        $wordData = array_values($wordData);

        if (!(isset($wordData[0]) && is_string($wordData[0]) && mb_strlen(trim($wordData[0])) > 0))
        {
            throw new \RuntimeException('First value must be a valid word.');
        }

        if (isset($wordData[1]) && !is_bool($wordData[1]))
        {
            throw new \RuntimeException('Second value must be a boolean or omitted.');
        }

        if (isset($wordData[2]) && !is_bool($wordData[2]))
        {
            throw new \RuntimeException('Third value must be a boolean or omitted.');
        }

        return $wordData;
    }
}