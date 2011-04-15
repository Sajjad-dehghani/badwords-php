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

class AbstractFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractFile
     */
    protected $indexStub;

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures/Csv';
    }

    protected function setUp()
    {
        $this->indexStub = $this->getMock(
            '\Badword\Index\AbstractFile',
            array('getFileType', 'loadWordsDataFromSource'),
            array($this->getFixtureDir().'/words.csv')
        );

        $this->indexStub->expects($this->any())
                        ->method('getFileType')
                        ->will($this->returnValue('mock'));
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
            array(true, '/i/dont/exist.file'),
            array(true, $this->getFixtureDir()),
            array(false, $this->getFixtureDir().'/words.csv'),
        );
    }

    /**
     * @dataProvider dataProviderSettingPath
     */
    public function testSettingPath($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Index\AbstractFile', $this->indexStub->setPath($data));
        $this->assertEquals(realpath($data), $this->indexStub->getPath());
    }

    public function testGetId()
    {
        $this->assertEquals('mock_'.md5($this->getFixtureDir().'/words.csv'), $this->indexStub->getId());
    }
}