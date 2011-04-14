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

    protected function getFixturePath()
    {
        return __DIR__.'/Fixtures/words.csv';
    }

    protected function setUp()
    {
        $this->indexStub = new Csv($this->getFixturePath());
    }
    
    public function testConstruct()
    {
        $csvIndex = new Csv($this->getFixturePath());
        $this->assertEquals($this->getFixturePath(), $csvIndex->getPath());

        $this->assertInstanceOf('\Badword\Cache', $csvIndex->getCache());
        $this->assertInstanceOf('\Badword\Cache\None', $csvIndex->getCache());
    }

    public function dataProviderSettingPath()
    {
        return array(
            array(true, array('foo')),
            array(true, true),
            array(true, false),
            array(true, null),
            array(true, 0),
            array(true, 1),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(true, '/i/dont/exist.csv'),
            array(true, __DIR__.'/Fixtures/'),
            array(false, $this->getFixturePath()),
        );
    }

    /**
     * @dataProvider dataProviderSettingPath
     */
    public function testSettingPath($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Index\Csv', $this->indexStub->setPath($data));
        $this->assertEquals(realpath($data), $this->indexStub->getPath());
    }

    public function testGetId()
    {
        $this->assertEquals('csv_'.md5($this->getFixturePath()), $this->indexStub->getId());
    }

    public function dataProviderGetWords()
    {
        return array(
            array(true, __DIR__.'/Fixtures/Csv/empty.csv'),
            array(true, __DIR__.'/Fixtures/Csv/invalid_word.csv'),
            array(true, __DIR__.'/Fixtures/Csv/invalid_must_start_word.csv'),
            array(true, __DIR__.'/Fixtures/Csv/invalid_must_end_word.csv'),
            array(false, $this->getFixturePath()),
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