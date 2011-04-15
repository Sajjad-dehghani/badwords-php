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

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Csv
     */
    protected $dictionaryStub;

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures/Csv';
    }

    protected function setUp()
    {
        $this->dictionaryStub = new Csv($this->getFixtureDir().'/words.csv');
    }

    public function testGetId()
    {
        $this->assertEquals('csv_'.md5($this->getFixtureDir().'/words.csv'), $this->dictionaryStub->getId());
    }

    public function dataProviderGetWords()
    {
        return array(
            array(true, $this->getFixtureDir().'/empty.csv'),
            array(true, $this->getFixtureDir().'/invalid_word.csv'),
            array(true, $this->getFixtureDir().'/invalid_must_start_word.csv'),
            array(true, $this->getFixtureDir().'/invalid_must_end_word.csv'),
            array(false, $this->getFixtureDir().'/words.csv'),
        );
    }

    /**
     * @dataProvider dataProviderGetWords
     */
    public function testGetWords($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\RuntimeException' : null);
        
        $dictionary = new Csv($data);

        $words = $dictionary->getWords();
        $this->assertInternalType('array', $words);
        $this->assertEquals(8, count($words));
        
        foreach($words as $key => $word)
        {
            $this->assertInstanceOf('\Badword\Word', $word);
        }
    }
}