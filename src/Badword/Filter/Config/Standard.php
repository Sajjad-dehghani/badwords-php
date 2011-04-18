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

use Badword\Filter\Config;
use Badword\Filter\Config\Rule\Character;
use Badword\Filter\Config\Rule\MustStartWord;
use Badword\Filter\Config\Rule\MustEndWord;
use Badword\Filter\Config\Rule\Whitespace;

/**
 * Standard defines a default Config to use for the Filter.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Standard extends Config
{
    /**
     * Constructs a new Standard Config.
     */
    public function __construct()
    {
        parent::__construct();

        // Vowels
        $this->addRules(array(
            new Character('a', array('@', '*'), true, 2),
            new Character('e', array('3', '*'), true, 2),
            new Character('i', array('1', '!', '*'), true, 2),
            new Character('o', array('0', '*'), true, 2),
            new Character('u', array('*'), true, 2)
        ));

        // Consonants
        $this->addRules(array(
            new Character('b', array('8')),
            new Character('c', array('*')),
            new Character('h', array('*')),
            new Character('l', array('1')),
            new Character('s', array('5', '$'), false, 2)
        ));

        // Whitespace
        $this->addRule(new Whitespace(array('!', '?')));

        // Start/End Word
        $this->addPostRules(array(
            new MustStartWord(),
            new MustEndWord()
        ));
    }
}