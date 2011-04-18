<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Filter\Config\Rule;

use Badword\Filter\Config\Rule;
use Badword\Word;

/**
 * MustStartWord defines the "start word" detection rule of a Word.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class MustStartWord implements Rule
{
    /**
     * {@inheritdoc}
     */
    public function apply($regExp, Word $word)
    {
        // If the Word must exist at the start of a word only
        if($word->getMustStartWord())
        {
            // Add word boundary detection
            $regExp = '\b'.$regExp;
        }

        return $regExp;
    }
}