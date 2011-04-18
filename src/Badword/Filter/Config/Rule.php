<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Filter\Config;

use Badword\Word;

/**
 * Rule defines a specific rule for the Config to implement.
 * 
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
interface Rule
{
    /**
     * Applies the Rule to the data using the provided Word.
     *
     * @param string $regExp
     * @param Word $word
     *
     * @return string The generated $regExp.
     */
    public function apply($regExp, Word $word);
}