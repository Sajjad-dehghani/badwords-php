<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Index;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Csv
     */
    protected $indexStub;

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures/Csv';
    }

    protected function setUp()
    {
        $this->indexStub = new Csv($this->getFixtureDir().'/words.csv');
    }

    public function testGetId()
    {
        $this->assertEquals('csv_'.md5($this->getFixtureDir().'/words.csv'), $this->indexStub->getId());
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
        
        $index = new Csv($data);

        $words = $index->getWords();
        $this->assertInternalType('array', $words);
        $this->assertEquals(8, count($words));
        
        foreach($words as $key => $word)
        {
            $this->assertInstanceOf('\Badword\Word', $word);
        }
    }
}