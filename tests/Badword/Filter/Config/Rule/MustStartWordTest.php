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
            array($wordStub1, 'bazaars'),
            array($wordStub2, '\bbazaars'),
        );
    }

    /**
     * @dataProvider dataProviderApply
     */
    public function testApply(Word $word, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->ruleStub->apply($word->getWord(), $word));
    }
}