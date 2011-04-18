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
        $this->assertEquals('csv_a376ca034e7d6538415b2a2d615bc3df', $this->dictionaryStub->getId());

        $this->dictionaryStub->setMustStartWordDefault(true);
        $this->assertEquals('csv_58a1926785a95f3204cb01a652df86bf', $this->dictionaryStub->getId());

        $this->dictionaryStub->setMustEndWordDefault(true);
        $this->assertEquals('csv_25b3fcddbb3ac2f1707fe04351613a7c', $this->dictionaryStub->getId());
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
        $this->setExpectedException($expectError ? '\Badword\Dictionary\Exception' : null);
        
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