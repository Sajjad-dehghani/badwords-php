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

class AbstractIndexTest extends \PHPUnit_Framework_TestCase
{
    protected static $wordsData = array(
        array('foo', 0, 0),
        array('bar', 0, 1),
        array('moo', 1, 0),
        array('boo', 1, 1),
        array('shu', 1),
        array('kew')
    );

    /**
     * @var AbstractIndex
     */
    protected $indexStub;

    protected function setUp()
    {
        $this->indexStub = $this->getMock('\Badword\Index\AbstractIndex', array('loadWordsDataFromSource', 'getId'));

        $this->indexStub->expects($this->any())
                        ->method('getId')
                        ->will($this->returnValue('mock_index'));

        $this->indexStub->expects($this->any())
                        ->method('loadWordsDataFromSource')
                        ->will($this->returnValue(static::$wordsData));
    }
    
    public function testGetCache()
    {
        $this->assertInstanceOf('\Badword\Cache', $this->indexStub->getCache());
        $this->assertInstanceOf('\Badword\Cache\None', $this->indexStub->getCache());
    }

    public function dataProviderSettingMustEndWordDefault()
    {
        return array(
            array(true, array('foo')),
            array(true, null),
            array(true, 0),
            array(true, 1),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider dataProviderSettingMustEndWordDefault
     */
    public function testSettingMustEndWordDefault($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Index\AbstractIndex', $this->indexStub->setMustEndWordDefault($data));
        $this->assertEquals($data, $this->indexStub->getMustEndWordDefault());
    }

    public function dataProviderSettingMustStartWordDefault()
    {
        return array(
            array(true, array('foo')),
            array(true, null),
            array(true, 0),
            array(true, 1),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(false, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider dataProviderSettingMustStartWordDefault
     */
    public function testSettingMustStartWordDefault($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Index\AbstractIndex', $this->indexStub->setMustStartWordDefault($data));
        $this->assertEquals($data, $this->indexStub->getMustStartWordDefault());
    }

    public function testGetWords()
    {
        $words = $this->indexStub->getWords();
        $this->assertInternalType('array', $words);
        $this->assertEquals(count(static::$wordsData), count($words));

        foreach($words as $key => $word)
        {
            $this->assertInstanceOf('\Badword\Word', $word);
            $this->assertEquals(static::$wordsData[$key][0], $word->getWord());
            $this->assertEquals(isset(static::$wordsData[$key][1]) && static::$wordsData[$key][1] ? true : false, $word->getMustStartWord());
            $this->assertEquals(isset(static::$wordsData[$key][2]) && static::$wordsData[$key][2] ? true : false, $word->getMustEndWord());
        }
    }
}