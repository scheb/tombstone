<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Php;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;

class PhpReportGenerator implements ReportGeneratorInterface
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public static function create(array $config, ConsoleOutputInterface $consoleOutput): ReportGeneratorInterface
    {
        return new self($config['report']['php']);
    }

    public function getName(): string
    {
        return 'PHP';
    }

    public function generate(AnalyzerResult $result): void
    {
        $serialized = str_replace("'", "\\'", serialize($result));
        file_put_contents($this->filePath, "<?php declare(strict_types=1); return unserialize('".$serialized."');");
    }
}
