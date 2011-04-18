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

class CharacterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Character
     */
    protected $characterStub;

    protected function setUp()
    {
        $this->characterStub = new Character('a');
    }

    public function testConstruct()
    {
        $character = new Character('b');
        $this->assertEquals('b', $character->getCharacter());
        $this->assertEquals(array(), $character->getAlternativeCharacters());
        $this->assertFalse($character->getDetectRepetition());
        $this->assertNull($character->getCanBeRepeatedFor());

        $character = new Character('b', array('c', 'd', 'e'));
        $this->assertEquals('b', $character->getCharacter());
        $this->assertEquals(array('c', 'd', 'e'), $character->getAlternativeCharacters());
        $this->assertFalse($character->getDetectRepetition());
        $this->assertNull($character->getCanBeRepeatedFor());

        $character = new Character('b', array('c', 'd'), true);
        $this->assertEquals('b', $character->getCharacter());
        $this->assertEquals(array('c', 'd'), $character->getAlternativeCharacters());
        $this->assertTrue($character->getDetectRepetition());
        $this->assertNull($character->getCanBeRepeatedFor());

        $character = new Character('b', array('d', 'e'), true, 3);
        $this->assertEquals('b', $character->getCharacter());
        $this->assertEquals(array('d', 'e'), $character->getAlternativeCharacters());
        $this->assertTrue($character->getDetectRepetition());
        $this->assertEquals(3, $character->getCanBeRepeatedFor());
    }

    public function dataProviderAddAlternativeCharacter()
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
            array(true, 'fd'),
            array(false, 'f')
        );
    }

    /**
     * @dataProvider dataProviderAddAlternativeCharacter
     */
    public function testAddAlternativeCharacter($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('Badword\Filter\Config\Rule\Character', $this->characterStub->addAlternativeCharacter($data));
        $this->assertEquals(array($data), $this->characterStub->getAlternativeCharacters());
    }

    public function testAddingAlternativeCharacter()
    {
        $this->characterStub->addAlternativeCharacter('a');
        $this->assertEquals(array('a'), $this->characterStub->getAlternativeCharacters());

        $this->characterStub->addAlternativeCharacter('a');
        $this->assertEquals(array('a'), $this->characterStub->getAlternativeCharacters());

        try
        {
            $this->characterStub->addAlternativeCharacters(array('a', 'foo', 'c'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->characterStub->addAlternativeCharacters(array('a', 'b', 'c'));
        $this->assertEquals(array('a', 'b', 'c'), $this->characterStub->getAlternativeCharacters());

        $this->characterStub->addAlternativeCharacters(array('test' => 'c', 'd', 'test2' => 'e', 'test3' => 'e'));
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'), $this->characterStub->getAlternativeCharacters());

        $this->characterStub->addAlternativeCharacter('D');
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'), $this->characterStub->getAlternativeCharacters());

        $this->characterStub->addAlternativeCharacters(array('test' => 'G', 'F', 'test2' => 'D'));
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e', 'g', 'f'), $this->characterStub->getAlternativeCharacters());

        try
        {
            $this->characterStub->setAlternativeCharacters(array('a', 'foo', 'c'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->characterStub->setAlternativeCharacters(array('x', 'Y', 'z', 'Z'));
        $this->assertEquals(array('x', 'y', 'z'), $this->characterStub->getAlternativeCharacters());
    }

    public function dataProviderSettingCharacter()
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
            array(true, 'fd'),
            array(false, 'f'),
            array(false, ' ')
        );
    }

    /**
     * @dataProvider dataProviderSettingCharacter
     */
    public function testSettingCharacter($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('Badword\Filter\Config\Rule\Character', $this->characterStub->setCharacter($data));
        $this->assertEquals($data, $this->characterStub->getCharacter());
    }

    public function dataProviderSettingCanBeRepeatedFor()
    {
        return array(
            array(true, array('foo')),
            array(true, true),
            array(true, false),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(true, 0),
            array(true, -1),
            array(false, null),
            array(false, 1)
        );
    }

    /**
     * @dataProvider dataProviderSettingCanBeRepeatedFor
     */
    public function testSettingCanBeRepeatedFor($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('Badword\Filter\Config\Rule\Character', $this->characterStub->setCanBeRepeatedFor($data));
        $this->assertEquals($data, $this->characterStub->getCanBeRepeatedFor());
    }

    public function dataProviderSettingDetectRepetition()
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
            array(false, false)
        );
    }

    /**
     * @dataProvider dataProviderSettingDetectRepetition
     */
    public function testSettingDetectRepetition($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('Badword\Filter\Config\Rule\Character', $this->characterStub->setDetectRepetition($data));
        $this->assertEquals($data, $this->characterStub->getDetectRepetition());
    }
}