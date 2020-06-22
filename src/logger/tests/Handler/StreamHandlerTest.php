<?php

declare(strict_types=1);

/*
 * Based on the StreamHandler from Monolog
 * https://github.com/Seldaek/monolog
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 */

namespace Scheb\Tombstone\Test\Handler;

use Scheb\Tombstone\Handler\StreamHandler;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;
use Scheb\Tombstone\Test\Stubs\LabelFormatter;
use Scheb\Tombstone\Test\TestCase;
use Scheb\Tombstone\Vampire;

class StreamHandlerTest extends TestCase
{
    public function getRecord($label = 'label'): Vampire
    {
        return VampireFixture::getVampire($label);
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWrite(): void
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
    public function testWriteCreatesTheStreamResource(): void
    {
        $handler = new StreamHandler('php://memory');
        $handler->log($this->getRecord());
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteLocking(): void
    {
        $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'monolog_locked_log';
        $handler = new StreamHandler($temp, null, true);
        $handler->log($this->getRecord());
        $this->expectNotToPerformAssertions();
    }

    /**
     * @expectedException \LogicException
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteMissingResource(): void
    {
        $handler = new StreamHandler(null);
        $handler->log($this->getRecord());
    }

    /**
     * @dataProvider provideInvalidArguments
     * @expectedException \InvalidArgumentException
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     */
    public function testWriteInvalidArgument($invalidArgument): void
    {
        new StreamHandler($invalidArgument);
    }

    public function provideInvalidArguments(): array
    {
        return [
            [1],
            [[]],
            [['bogus://url']],
        ];
    }

    /**
     * @expectedException \UnexpectedValueException
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteInvalidResource(): void
    {
        $handler = new StreamHandler('bogus://url');
        $handler->log($this->getRecord());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingResource(): void
    {
        $handler = new StreamHandler('ftp://foo/bar/baz/'.rand(0, 10000));
        $handler->log($this->getRecord());
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingPath(): void
    {
        $handler = new StreamHandler(sys_get_temp_dir().'/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingFileResource(): void
    {
        $handler = new StreamHandler('file://'.sys_get_temp_dir().'/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
        $this->expectNotToPerformAssertions();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp /There is no existing directory at/
     * @covers \Scheb\Tombstone\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Handler\StreamHandler::log
     */
    public function testWriteNonExistingAndNotCreatablePath(): void
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
    public function testWriteNonExistingAndNotCreatableFileResource(): void
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Permissions checks can not run on windows');
        }
        $handler = new StreamHandler('file:///foo/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
    }
}
