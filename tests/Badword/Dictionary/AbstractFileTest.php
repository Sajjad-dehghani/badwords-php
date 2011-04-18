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

class AbstractFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractFile
     */
    protected $dictionaryStub;

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures/Csv';
    }

    protected function setUp()
    {
        $this->dictionaryStub = $this->getMock(
            '\Badword\Dictionary\AbstractFile',
            array('getFileType', 'loadWordsDataFromSource'),
            array($this->getFixtureDir().'/words.csv')
        );

        $this->dictionaryStub->expects($this->any())
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
        $this->assertInstanceOf('\Badword\Dictionary\AbstractFile', $this->dictionaryStub->setPath($data));
        $this->assertEquals(realpath($data), $this->dictionaryStub->getPath());
    }

    public function testGetId()
    {
        $this->assertEquals('mock_a376ca034e7d6538415b2a2d615bc3df', $this->dictionaryStub->getId());

        $this->dictionaryStub->setMustStartWordDefault(true);
        $this->assertEquals('mock_58a1926785a95f3204cb01a652df86bf', $this->dictionaryStub->getId());

        $this->dictionaryStub->setMustEndWordDefault(true);
        $this->assertEquals('mock_25b3fcddbb3ac2f1707fe04351613a7c', $this->dictionaryStub->getId());
    }
}