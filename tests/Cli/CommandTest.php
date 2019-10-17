<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Test\Cli;

use Scheb\Tombstone\Analyzer\Cli\Command;
use Scheb\Tombstone\Analyzer\Test\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandTest extends TestCase
{
    private const SOURCE_DIR = __DIR__.'/../_application';
    private const LOG_DIR = __DIR__.'/../_logs';
    private const REPORT_DIR = __DIR__.'/../_report';
    private const EXPECTED_REPORT_FILES = [
        '.gitkeep',
        'css',
        'css/bootstrap.min.css',
        'css/style.css',
        'dashboard.html',
        'icons',
        'icons/file-code.svg',
        'icons/file-directory.svg',
        'img',
        'img/cross.png',
        'img/deleted.png',
        'img/vampire.png',
        'index.html',
        'src',
        'src/App',
        'src/App/SampleClass.php.html',
        'src/App/index.html',
        'src/functions.php.html',
        'src/index.html',
        'tombstone-report.php',
    ];

    protected function setUp()
    {
        $this->clean();
    }

    protected function tearDown()
    {
        $this->clean();
    }

    /**
     * @test
     * @covers \Scheb\Tombstone\Analyzer\Cli\Command
     */
    public function generate_logsAndSourceGiven_createHtmlReport(): void
    {
        $this->runTestApplication();
        $this->generateReport();
        $this->assertReportFileStructure();
    }

    private function runTestApplication(): void
    {
        exec('php '.self::SOURCE_DIR.'/run.php');
    }

    private function generateReport(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = new BufferedOutput();

        $input
            ->expects($this->any())
            ->method('getOption')
            ->willReturnMap([
                ['config', self::SOURCE_DIR.'/tombstone.yml'],
            ]);

        $command = new Command();
        $command->run($input, $output);
    }

    private function assertReportFileStructure(): void
    {
        $directoryListing = $this->listReportDirectory();
        $this->assertEquals(self::EXPECTED_REPORT_FILES, $directoryListing);
    }

    private function listReportDirectory(): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::REPORT_DIR, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        $relativePathStart = strlen(realpath(self::REPORT_DIR)) + 1;
        $files = [];
        foreach ($iterator as $fileInfo) {
            $files[] = str_replace('\\', '/', substr($fileInfo->getRealPath(), $relativePathStart));
        }
        sort($files);

        return $files;
    }

    private function clean(): void
    {
        $this->clearDirectory(self::LOG_DIR);
        $this->clearDirectory(self::REPORT_DIR);
    }

    private function clearDirectory(string $directory): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileInfo) {
            if ('.gitkeep' === $fileInfo->getBaseName()) {
                continue;
            }
            $cmd = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
            @$cmd($fileInfo->getRealPath());
        }
    }
}
