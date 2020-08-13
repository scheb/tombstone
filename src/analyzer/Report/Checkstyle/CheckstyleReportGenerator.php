<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Checkstyle;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;
use Scheb\Tombstone\Core\Model\Tombstone;

class CheckstyleReportGenerator implements ReportGeneratorInterface
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
        return new self($config['report']['checkstyle']);
    }

    public function getName(): string
    {
        return 'Checkstyle';
    }

    public function generate(AnalyzerResult $result): void
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $rootNode = $dom->appendChild($dom->createElement('checkstyle'));

        foreach ($result->getFileResults() as $fileResult) {
            if ($fileResult->getUndeadCount() > 0) {
                /** @var \DOMElement $fileNode */
                $fileNode = $rootNode->appendChild($dom->createElement('file'));
                $fileNode->setAttribute('name', $fileResult->getFile()->getAbsolutePath());

                foreach ($fileResult->getUndead() as $tombstone) {
                    $errorNode = $this->createError($dom, $tombstone);
                    $fileNode->appendChild($errorNode);
                }
            }
        }

        $dom->formatOutput = true;

        file_put_contents($this->filePath, $dom->saveXML());
    }

    private function createError(\DOMDocument $dom, Tombstone $tombstone): \DOMElement
    {
        $error = $dom->createElement('error');
        $error->setAttribute('severity', 'error');
        $error->setAttribute('source', 'Tombstone.Analyzer.undead');
        $error->setAttribute('message', $this->getMessage($tombstone));
        $error->setAttribute('line', (string) $tombstone->getLine());

        return $error;
    }

    private function getMessage(Tombstone $tombstone): string
    {
        return sprintf('Tombstone "%s" was called', (string) $tombstone).$this->getCalledBy($tombstone);
    }

    /**
     * @psalm-type list<string|null>
     */
    private function getCalledBy(Tombstone $tombstone): string
    {
        $vampires = $tombstone->getVampires();
        $numVampires = \count($vampires);
        if (0 === $numVampires) {
            return '';
        }

        $invoker = array_shift($vampires)->getInvoker();
        $calledBy = sprintf(' by "%s"', $invoker ?: 'global scope');

        $numAdditionalVampires = $numVampires - 1;
        if ($numAdditionalVampires > 0) {
            $calledBy .= ' and '.$numAdditionalVampires.' more caller'.($numAdditionalVampires > 1 ? 's' : '');
        }

        return $calledBy;
    }
}
