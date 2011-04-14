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
    protected static $wordsStub = array(
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
        $this->indexStub = $this->getMock('\Badword\Index\AbstractIndex', array('loadWordsFromSource', 'getId'));

        $this->indexStub->expects($this->any())
                        ->method('getId')
                        ->will($this->returnValue('mock_index'));

        $this->indexStub->expects($this->any())
                        ->method('loadWordsFromSource')
                        ->will($this->returnValue(static::$wordsStub));
    }
    
    public function testGetCache()
    {
        $this->assertInstanceOf('\Badword\Cache', $this->indexStub->getCache());
        $this->assertInstanceOf('\Badword\Cache\None', $this->indexStub->getCache());
    }

    public function testGetWords()
    {
        $words = $this->indexStub->getWords();
        $this->assertInternalType('array', $words);
        $this->assertEquals(count(static::$wordsStub), count($words));

        foreach($words as $key => $word)
        {
            $this->assertInstanceOf('\Badword\Word', $word);
            $this->assertEquals(static::$wordsStub[$key][0], $word->getWord());
            $this->assertEquals(isset(static::$wordsStub[$key][1]) && static::$wordsStub[$key][1] ? true : false, $word->getMustStartWord());
            $this->assertEquals(isset(static::$wordsStub[$key][2]) && static::$wordsStub[$key][2] ? true : false, $word->getMustEndWord());
        }
    }
}