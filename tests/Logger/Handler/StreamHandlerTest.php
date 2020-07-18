<?php

declare(strict_types=1);

/*
 * Based on the StreamHandler from Monolog
 * https://github.com/Seldaek/monolog
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 */

namespace Scheb\Tombstone\Tests\Logger\Handler;

use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Logger\Handler\StreamHandler;
use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Tests\VampireFixture;

class StreamHandlerTest extends TestCase
{
    public function getRecord($label = 'label'): Vampire
    {
        return VampireFixture::getVampire($label);
    }

    /**
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
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
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteCreatesTheStreamResource(): void
    {
        $handler = new StreamHandler('php://memory');
        $handler->log($this->getRecord());
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteLocking(): void
    {
        $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'monolog_locked_log';
        $handler = new StreamHandler($temp, null, true);
        $handler->log($this->getRecord());
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteMissingResource(): void
    {
        $this->expectException('LogicException');
        $handler = new StreamHandler(null);
        $handler->log($this->getRecord());
    }

    /**
     * @dataProvider provideInvalidArguments
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     */
    public function testWriteInvalidArgument($invalidArgument): void
    {
        $this->expectException('InvalidArgumentException');
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
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteInvalidResource(): void
    {
        $this->expectException('UnexpectedValueException');
        $handler = new StreamHandler('bogus://url');
        $handler->log($this->getRecord());
    }

    /**
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteNonExistingResource(): void
    {
        $this->expectException('UnexpectedValueException');
        $handler = new StreamHandler('ftp://foo/bar/baz/'.rand(0, 10000));
        $handler->log($this->getRecord());
    }

    /**
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteNonExistingPath(): void
    {
        $handler = new StreamHandler(sys_get_temp_dir().'/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteNonExistingFileResource(): void
    {
        $handler = new StreamHandler('file://'.sys_get_temp_dir().'/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteNonExistingAndNotCreatablePath(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessageRegExp('/There is no existing directory at/');
        if (\defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Permissions checks can not run on windows');
        }
        $handler = new StreamHandler('/foo/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
    }

    /**
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::__construct
     * @covers \Scheb\Tombstone\Logger\Handler\StreamHandler::log
     */
    public function testWriteNonExistingAndNotCreatableFileResource(): void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessageRegExp('/There is no existing directory at/');
        if (\defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Permissions checks can not run on windows');
        }
        $handler = new StreamHandler('file:///foo/bar/'.rand(0, 10000).DIRECTORY_SEPARATOR.rand(0, 10000));
        $handler->log($this->getRecord());
    }
}
