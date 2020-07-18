<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Formatter\AnalyzerLogFormatter;
use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Model\Vampire;

class AnalyzerLogHandler extends AbstractHandler
{
    private const LOG_FILE_NAME = '%s-%s.tombstone';

    /**
     * @var StreamHandler[]
     */
    private $logStreams = [];

    /**
     * @var string
     */
    private $logDir;

    /**
     * @var int|null
     */
    private $sizeLimit;

    /**
     * @var int|null
     */
    private $filePermission;

    /**
     * @var bool
     */
    private $useLocking;

    public function __construct(string $logDir, int $sizeLimit = null, ?int $filePermission = null, bool $useLocking = false)
    {
        $this->logDir = $logDir;
        $this->sizeLimit = $sizeLimit;
        $this->filePermission = $filePermission;
        $this->useLocking = $useLocking;
    }

    public function __destruct()
    {
        parent::__destruct();
        $this->close();
    }

    public function close(): void
    {
        foreach ($this->logStreams as $stream) {
            $stream->close();
        }
    }

    public function log(Vampire $vampire): void
    {
        $logFile = $this->getLogFile($vampire);
        if (!$this->sizeLimitReached($logFile)) {
            $logger = $this->getLogStream($logFile);
            $logger->log($vampire);
        }
    }

    private function getLogFile(Vampire $vampire): string
    {
        $date = date('Ymd');
        $hash = $vampire->getTombstone()->getHash();

        return $this->logDir.'/'.sprintf(self::LOG_FILE_NAME, $hash, $date);
    }

    private function getLogStream(string $logFile): StreamHandler
    {
        if (!isset($this->logStreams[$logFile])) {
            $handler = new StreamHandler($logFile, $this->filePermission, $this->useLocking);
            $handler->setFormatter($this->getFormatter());
            $this->logStreams[$logFile] = $handler;
        }

        return $this->logStreams[$logFile];
    }

    private function sizeLimitReached(string $logFile): bool
    {
        if ($this->sizeLimit <= 0 || !file_exists($logFile)) {
            return false;
        }

        clearstatcache(false, $logFile);

        return filesize($logFile) >= $this->sizeLimit;
    }

    public function flush(): void
    {
        foreach ($this->logStreams as $stream) {
            $stream->flush();
        }
    }

    public function setFormatter(FormatterInterface $formatter): void
    {
        throw new \LogicException('Formatter of AnalyzerLogHandler cannot be changed.');
    }

    protected function getDefaultFormatter(): \Scheb\Tombstone\Formatter\FormatterInterface
    {
        return new AnalyzerLogFormatter();
    }
}
