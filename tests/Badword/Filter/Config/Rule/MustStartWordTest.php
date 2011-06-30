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

class MustStartWordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MustStartWord
     */
    protected $ruleStub;

    protected function setUp()
    {
        $this->ruleStub = new MustStartWord();
    }

    public function dataProviderApply()
    {
        $wordStub1 = new Word('bazaars');
        $wordStub2 = new Word('bazaars');
        $wordStub2->setMustStartWord(true);

        return array(
            array($wordStub1, '()bazaars'),
            array($wordStub2, '(^|'.MustStartWord::REGEXP.')bazaars'),
        );
    }

    /**
     * @dataProvider dataProviderApply
     */
    public function testApply(Word $word, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->ruleStub->apply($word->getWord(), $word));
    }
    
    public function dataProviderRegExp()
    {
        return array(
            array('start@nimal'),
            array('@nimal', ''),
            array('lorem @nimal', ' '),
            array('lorem.@nimal', '.'),
            array('lorem-@nimal', '-'),
            array('lorem_@nimal', '_'),
            array('lorem!@nimal', '!'),
            array('lorem"@nimal', '"'),
            array('lorem\'@nimal', '\''),
            array('lorem^@nimal', '^'),
            array('lorem&@nimal', '&'),
            array('lorem*@nimal', '*'),
            array('lorem(@nimal', '('),
            array('lorem)@nimal', ')'),
            array('lorem=@nimal', '='),
            array('lorem+@nimal', '+'),
            array('lorem@@nimal', '@'),
        );
    }
    
    /**
     * @dataProvider dataProviderRegExp
     */
    public function testRegExp($string, $boundaryMatch = null)
    {
        $word = new Word('@nimal');
        $word->setMustStartWord(true);
        $regExp = $this->ruleStub->apply($word->getWord(), $word);
        
        $this->assertEquals(($boundaryMatch !== null ? 1 : 0), preg_match_all('/'.$regExp.'/iu', $string, $matches));
        if($boundaryMatch !== null)
        {
            $this->assertEquals(2, count($matches));
            $this->assertEquals(array($boundaryMatch . '@nimal'), $matches[0]);
            $this->assertEquals(array($boundaryMatch), $matches[1]);
        }
    }
}