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

class MustEndWordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MustEndWord
     */
    protected $ruleStub;

    protected function setUp()
    {
        $this->ruleStub = new MustEndWord();
    }

    public function dataProviderApply()
    {
        $wordStub1 = new Word('bazaars');
        $wordStub2 = new Word('bazaars');
        $wordStub2->setMustEndWord(true);

        return array(
            array($wordStub1, 'bazaars()'),
            array($wordStub2, 'bazaars($|'.MustEndWord::REGEXP.')'),
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
            array('magn@end'),
            array('magn@', ''),
            array('magn@ lorem', ' '),
            array('magn@.lorem', '.'),
            array('magn@-lorem', '-'),
            array('magn@_lorem', '_'),
            array('magn@!lorem', '!'),
            array('magn@"lorem', '"'),
            array('magn@\'lorem', '\''),
            array('magn@^lorem', '^'),
            array('magn@&lorem', '&'),
            array('magn@*lorem', '*'),
            array('magn@(lorem', '('),
            array('magn@)lorem', ')'),
            array('magn@=lorem', '='),
            array('magn@+lorem', '+'),
            array('magn@@lorem', '@'),
        );
    }
    
    /**
     * @dataProvider dataProviderRegExp
     */
    public function testRegExp($string, $boundaryMatch = null)
    {
        $word = new Word('magn@');
        $word->setMustEndWord(true);
        $regExp = $this->ruleStub->apply($word->getWord(), $word);
        
        $this->assertEquals(($boundaryMatch !== null ? 1 : 0), preg_match_all('/'.$regExp.'/iu', $string, $matches));
        if($boundaryMatch !== null)
        {
            $this->assertEquals(2, count($matches));
            $this->assertEquals(array('magn@' . $boundaryMatch), $matches[0]);
            $this->assertEquals(array($boundaryMatch), $matches[1]);
        }
    }
}