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

/**
 * Dictionary gets a list of bad words.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
interface Dictionary
{
    /**
     * Gets the unique ID for the Dictionary.
     *
     * @return string
     */
    public function getId();
    
    /**
     * Gets the Words.
     *
     * @return array Array of Word objects.
     */
    public function getWords();
}