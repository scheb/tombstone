<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Cli;

use Scheb\Tombstone\Analyzer\Cli\Application;
use Scheb\Tombstone\Tests\DirectoryHelper;
use Scheb\Tombstone\Tests\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

class ApplicationTest extends TestCase
{
    private const ROOT_DIR = __DIR__.'/../../..';
    private const APPLICATION_DIR = self::ROOT_DIR.'/app';
    private const LOG_DIR = self::APPLICATION_DIR.'/logs';
    private const REPORT_DIR = self::APPLICATION_DIR.'/report';
    private const EXPECTED_REPORT_FILES = [
        '.gitkeep',
        '_css',
        '_css/bootstrap.min.css',
        '_css/style.css',
        '_icons',
        '_icons/cross.svg',
        '_icons/file-code.svg',
        '_icons/file-directory.svg',
        '_icons/home.svg',
        '_icons/tombstone.svg',
        '_icons/trash.svg',
        '_icons/vampire.svg',
        'checkstyle.xml',
        'dashboard.html',
        'index.html',
        'src',
        'src/App',
        'src/App/SampleClass.php.html',
        'src/App/index.html',
        'src/functions.php.html',
        'src/index.html',
        'tombstone-report.php',
    ];

    protected function setUp(): void
    {
        $this->clean();
    }

    protected function tearDown(): void
    {
        $this->clean();
    }

    /**
     * @test
     * @covers \Scheb\Tombstone\Analyzer\Cli\Application
     * @covers \Scheb\Tombstone\Analyzer\Cli\AnalyzeCommand
     */
    public function generate_logsAndSourceGiven_createHtmlReport(): void
    {
        $this->runTestApplication();
        $this->generateReport();
        $this->assertReportFileStructure();
    }

    private function runTestApplication(): void
    {
        exec('php '.self::APPLICATION_DIR.'/run.php');
    }

    private function generateReport(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = new BufferedOutput();

        $input
            ->expects($this->any())
            ->method('getOption')
            ->willReturnMap([
                ['config', self::APPLICATION_DIR.'/tombstone.yml'],
            ]);

        $application = new Application();
        $application->setAutoExit(false);
        $returnCode = $application->run($input, $output);
        $this->assertEquals(0, $returnCode);
    }

    private function assertReportFileStructure(): void
    {
        $directoryListing = DirectoryHelper::listDirectory(self::REPORT_DIR);
        $this->assertEquals(self::EXPECTED_REPORT_FILES, $directoryListing);
    }

    private function clean(): void
    {
        DirectoryHelper::clearDirectory(self::LOG_DIR);
        DirectoryHelper::clearDirectory(self::REPORT_DIR);
    }
}
