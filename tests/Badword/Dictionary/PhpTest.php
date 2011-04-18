<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Dictionary;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Php
     */
    protected $dictionaryStub;

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures/Php';
    }

    protected function setUp()
    {
        $this->dictionaryStub = new Php($this->getFixtureDir().'/words.php');
    }

    public function testGetId()
    {
        $this->assertEquals('php_0acd5ae3d381cf109c86466b3cb7af73', $this->dictionaryStub->getId());
    }

    public function dataProviderGetWords()
    {
        return array(
            array(true, $this->getFixtureDir().'/no_words_variable.php'),
            array(true, $this->getFixtureDir().'/invalid_format.php'),
            array(true, $this->getFixtureDir().'/invalid_word_data.php'),
            array(true, $this->getFixtureDir().'/invalid_word.php'),
            array(true, $this->getFixtureDir().'/invalid_must_start_word.php'),
            array(true, $this->getFixtureDir().'/invalid_must_end_word.php'),
            array(false, $this->getFixtureDir().'/words.php'),
        );
    }

    /**
     * @dataProvider dataProviderGetWords
     */
    public function testGetWords($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\Badword\Dictionary\Exception' : null);
        
        $dictionary = new Php($data);

        $words = $dictionary->getWords();
        $this->assertInternalType('array', $words);
        $this->assertEquals(8, count($words));
        
        foreach($words as $key => $word)
        {
            $this->assertInstanceOf('\Badword\Word', $word);
        }
    }
}