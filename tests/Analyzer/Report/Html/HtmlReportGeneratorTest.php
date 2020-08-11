<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report\Html;

use Scheb\Tombstone\Analyzer\Report\Html\HtmlReportGenerator;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\BreadCrumbRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DashboardRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DirectoryItemRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DirectoryRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\FileRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\FileSourceCodeRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\FileTombstoneListRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\PhpFileFormatter;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\PhpSyntaxHighlighter;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Tests\Analyzer\Report\fixtures\AnalyzerResultFixture;
use Scheb\Tombstone\Tests\DirectoryHelper;
use Scheb\Tombstone\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class HtmlReportGeneratorTest extends TestCase
{
    private const REPORT_DIR = __DIR__.'/report';
    private const EXPECTED_REPORT_FILES = [
        'Bar',
        'Bar/Class2.php.html',
        'Bar/Class3.php.html',
        'Bar/index.html',
        'Class1.php.html',
        '_css',
        '_css/bootstrap.min.css',
        '_css/style.css',
        '_icons',
        '_icons/file-code.svg',
        '_icons/file-directory.svg',
        '_img',
        '_img/cross.png',
        '_img/deleted.png',
        '_img/vampire.png',
        'dashboard.html',
        'functions.php.html',
        'index.html',
    ];

    protected function setUp(): void
    {
        $this->clearReportDirectory();
    }

    protected function tearDown(): void
    {
        $this->clearReportDirectory();
    }

    private function clearReportDirectory(): void
    {
        if (file_exists(self::REPORT_DIR)) {
            DirectoryHelper::clearDirectory(self::REPORT_DIR);
            rmdir(self::REPORT_DIR);
        }
    }

    /**
     * @test
     */
    public function generate_resultGiven_exportedDataEqualsOriginalResult(): void
    {
        $sourceRootPath = new RootPath(__DIR__.'/../fixtures/source');
        $result = AnalyzerResultFixture::getAnalyzerResult();

        $breadCrumbRenderer = new BreadCrumbRenderer($sourceRootPath);
        $generator = new HtmlReportGenerator(
            self::REPORT_DIR,
            new DashboardRenderer(
                self::REPORT_DIR,
                $breadCrumbRenderer
            ),
            new DirectoryRenderer(
                self::REPORT_DIR,
                $breadCrumbRenderer,
                new DirectoryItemRenderer()
            ),
            new FileRenderer(
                self::REPORT_DIR,
                $breadCrumbRenderer,
                new FileTombstoneListRenderer(),
                new FileSourceCodeRenderer(new PhpFileFormatter(new PhpSyntaxHighlighter()))
            )
        );
        $generator->generate($result);

        $this->assertGeneratedFiles();
        $this->assertDashboard();
        $this->assertDirectoryIndex();
        $this->assertFileReport();
    }

    private function assertGeneratedFiles(): void
    {
        $directoryListing = DirectoryHelper::listDirectory(self::REPORT_DIR);
        $this->assertEquals(self::EXPECTED_REPORT_FILES, $directoryListing);
    }

    private function assertDashboard(): void
    {
        $crawler = $this->getCrawler('dashboard.html');
        $this->assertEquals('5 Tombstones', $this->getText($crawler->filter('.tombstones-total')));
        $this->assertEquals('3 Dead', $this->getText($crawler->filter('.tombstones-dead')));
        $this->assertEquals('2 Undead', $this->getText($crawler->filter('.tombstones-undead')));

        $expectedActiveTombstones = [];
        $expectedActiveTombstones['Bar/Class2.php'] = [
            [
                'inscription' => 'tombstone("2020-01-01", "Class2")',
                'type' => 'undead',
                'details' => 'in line 11 in method Foo\\Bar\\Class2->publicMethod was called by invoker2 was called by invoker3',
            ],
        ];
        $expectedActiveTombstones['Bar/Class3.php'] = [
            [
                'inscription' => 'tombstone("2020-01-01", "Class3")',
                'type' => 'dead',
                'details' => 'in line 11 in method Foo\\Bar\\Class3->someOtherMethod was not called for 31 weeks, 6 days',
            ],
        ];
        $expectedActiveTombstones['Class1.php'] = [
            [
                'inscription' => 'tombstone("2020-01-01", "Class1")',
                'type' => 'dead',
                'details' => 'in line 11 in method Foo\\Class1::staticMethod was not called for 31 weeks, 6 days',
            ],
        ];
        $expectedActiveTombstones['functions.php'] = [
            [
                'inscription' => 'tombstone("2020-01-01", "globalScope")',
                'type' => 'undead',
                'details' => 'in line 10 in global scope was called by invoker1',
            ],
            [
                'inscription' => 'tombstone("2020-01-01", "globalFunction")',
                'type' => 'dead',
                'details' => 'in line 7 in method globalFunction was not called for 31 weeks, 6 days',
            ],
        ];

        $activeTombstones = $this->extractDashboardTombstones($crawler->filter('.tombstones-active .tombstone-file'));
        $this->assertEquals($expectedActiveTombstones, $activeTombstones);

        $expectedDeletedTombstones = [];
        $expectedDeletedTombstones['Class1.php'] = [
            [
                'inscription' => 'tombstone("2020-01-01", "Class1")',
                'type' => 'deleted',
                'details' => 'in line 18 in method Foo\\Class1->deletedMethod was last called 27 weeks, 3 days ago',
            ],
        ];

        $deletedTombstones = $this->extractDashboardTombstones($crawler->filter('.tombstones-deleted .tombstone-file'));
        $this->assertEquals($expectedDeletedTombstones, $deletedTombstones);
    }

    private function extractDashboardTombstones(Crawler $crawler): array
    {
        $result = [];
        $crawler->each(function (Crawler $fileNode) use (&$result) {
            $this->extractDashboardTombstonesPerFile($fileNode, $result);
        });

        return $result;
    }

    private function extractDashboardTombstonesPerFile(Crawler $fileItem, array &$result): void
    {
        $fileName = $this->getText($fileItem->filter('h3'));
        $tombstones = $fileItem->filter('.tombstone-list li')->each(function (Crawler $tombstoneNode): array {
            $inscription = $this->getText($tombstoneNode->filter('h4'));
            $className = $tombstoneNode->attr('class');
            $type = $this->getDashboardTombstoneType($className);
            $details = $this->getText($tombstoneNode->filter('.tombstone-detail'));

            return ['inscription' => $inscription, 'type' => $type, 'details' => $details];
        });

        $result[$fileName] = $tombstones;
    }

    private function getDashboardTombstoneType(string $classes): ?string
    {
        if (false !== \strpos($classes, 'tombstone-undead')) {
            return 'undead';
        }
        if (false !== \strpos($classes, 'tombstone-dead')) {
            return 'dead';
        }
        if (false !== \strpos($classes, 'tombstone-deleted')) {
            return 'deleted';
        }

        return null;
    }

    private function assertDirectoryIndex(): void
    {
        $crawler = $this->getCrawler('index.html');

        $expectedDirectoryList = [
            'Bar' => ['total' => 2, 'dead' => 1, 'undead' => 1],
            'Class1.php' => ['total' => 1, 'dead' => 1, 'undead' => 0],
            'functions.php' => ['total' => 2, 'dead' => 1, 'undead' => 1],
        ];

        $directoryList = $this->extractDirectoryListing($crawler->filter('.directory-list tbody tr'));
        $this->assertEquals($expectedDirectoryList, $directoryList);
    }

    private function extractDirectoryListing(Crawler $directoryItems): array
    {
        $result = [];
        $directoryItems->each(function (Crawler $item) use (&$result) {
            $file = $this->getText($item->filter('td:nth-child(1)'));
            $total = $this->getText($item->filter('td:nth-child(3)'));
            $dead = $this->getText($item->filter('td:nth-child(4)'));
            $undead = $this->getText($item->filter('td:nth-child(5)'));

            $result[$file] = ['total' => $total, 'dead' => $dead, 'undead' => $undead];
        });

        return $result;
    }

    private function assertFileReport(): void
    {
        $crawler = $this->getCrawler('functions.php.html');

        $expectedTombstoneList = [
            [
                'inscription' => 'tombstone("2020-01-01", "globalFunction")',
                'line' => 7,
                'method' => 'globalFunction',
            ],
            [
                'inscription' => 'tombstone("2020-01-01", "globalScope")',
                'line' => 10,
                'method' => '',
            ],
        ];

        $tombstoneList = $this->extractFileTombstoneListing($crawler->filter('.file-tombstone-list tbody tr'));
        $this->assertEquals($expectedTombstoneList, $tombstoneList);

        $line7Class = $crawler->filter('.source-code tbody tr:nth-child(7)')->attr('class');
        $this->assertEquals('success icon-cross', $line7Class);
        $line10Class = $crawler->filter('.source-code tbody tr:nth-child(10)')->attr('class');
        $this->assertEquals('danger icon-vampire', $line10Class);
    }

    private function extractFileTombstoneListing(Crawler $listItems): array
    {
        return $listItems->each(function (Crawler $listItem) use (&$result) {
            $line = $this->getText($listItem->filter('td:nth-child(1)'));
            $inscription = $this->getText($listItem->filter('td:nth-child(2)'));
            $method = $this->getText($listItem->filter('td:nth-child(3)'));

            return ['inscription' => $inscription, 'line' => $line, 'method' => $method];
        });
    }

    private function getCrawler(string $relativeFilePath): Crawler
    {
        return new Crawler(file_get_contents(self::REPORT_DIR.'/'.$relativeFilePath));
    }

    private function getText(Crawler $crawler): string
    {
        // Although these are the default parameter values, it's necessary for backwards
        // compatibility with symfony/dom-crawler 4.4
        return $crawler->text(null, true);
    }
}
