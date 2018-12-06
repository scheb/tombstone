<?php

/*
 * Based on the StreamHandler from Monolog
 * https://github.com/Seldaek/monolog
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 */

namespace Monolog\Handler;

use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Handler\StreamHandler;
use Scheb\Tombstone\Tests\Fixtures\VampireFixture;
use Scheb\Tombstone\Tests\Stubs\LabelFormatter;
use Scheb\Tombstone\Vampire;

class StreamHandlerTest extends TestCase
{
    /**
     * @return Vampire
     */
    public function getRecord($label = 'label')
    {
        return VampireFixture::getVampire(null, null, $label);
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWrite()
    {
        $handle = fopen('php://memory', 'a+');
        $handler = new StreamHandler($handle);
        $handler->setFormatter(new LabelFormatter());
        $handler->log($this->getRecord('test'));
        $handler->log($this->getRecord('test2'));
        $handler->log($this->getRecord('test3'));
        fseek($handle, 0);
        $this->assertEquals('testtest2test3', fread($handle, 100));
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteCreatesTheStreamResource()
    {
        $handler = new StreamHandler('php://memory');
        $handler->log($this->getRecord());
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteLocking()
    {
        $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'monolog_locked_log';
        $handler = new StreamHandler($temp, null, true);
        $handler->log($this->getRecord());
    }

    /**
     * @expectedException \LogicException
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteMissingResource()
    {
        $handler = new StreamHandler(null);
        $handler->log($this->getRecord());
    }

    public function invalidArgumentProvider()
    {
        return array(
            array(1),
            array(array()),
            array(array('bogus://url')),
        );
    }

    /**
     * @dataProvider invalidArgumentProvider
     * @expectedException \InvalidArgumentException
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     */
    public function testWriteInvalidArgument($invalidArgument)
    {
        $handler = new StreamHandler($invalidArgument);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteInvalidResource()
    {
        $handler = new StreamHandler('bogus://url');
        $handler->log($this->getRecord());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingResource()
    {
        $handler = new StreamHandler('ftp://foo/bar/baz/'.rand(0, 10000));
        $handler->log($this->getRecord());
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingPath()
    {
        $handler = new StreamHandler(sys_get_temp_dir().'/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingFileResource()
    {
        $handler = new StreamHandler('file://'.sys_get_temp_dir().'/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp /There is no existing directory at/
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingAndNotCreatablePath()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Permissions checks can not run on windows');
        }
        $handler = new StreamHandler('/foo/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp /There is no existing directory at/
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingAndNotCreatableFileResource()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Permissions checks can not run on windows');
        }
        $handler = new StreamHandler('file:///foo/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
    }
}
