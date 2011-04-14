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
 * Index loads a list of bad words.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
interface Index
{
    /**
     * Gets the unique ID for the Index.
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