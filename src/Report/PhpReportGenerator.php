<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\AnalyzerResult;

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

    public function getName(): string
    {
        return 'PHP';
    }

    public function generate(AnalyzerResult $result): void
    {
        $serialized = str_replace("'", "\\'", serialize($result));
        file_put_contents($this->filePath, "<?php

declare(strict_types=1); return unserialize('".$serialized."');");
    }
}
