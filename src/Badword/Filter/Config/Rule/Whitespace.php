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

use Badword\Word;

/**
 * Whitespace defines the rule for whitespace in a Word.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Whitespace extends AbstractCharacter
{
    /**
     * {@inheritdoc}
     */
    public function apply($regExp, Word $word)
    {
        // Add repetition detection
        $regExp = preg_replace('/\s+/iu', '\\s+', $regExp);

        // If there are alternative characters that could be used in place of the whitespace
        if($this->getAlternativeCharacters())
        {
            // Add detection for them
            $alternativeCharacters = array_merge(array('\s'), $this->getAlternativeCharacters());
            $alternativeCharacters = preg_replace('/(\*|\?|\$|\^)/iu', '\\\$1', implode('|', $alternativeCharacters));
            $regExp = preg_replace('/\\\s/ui', '('.$alternativeCharacters.')', $regExp);
        }

        return $regExp;
    }
}