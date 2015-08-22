<?php
namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Formatter\FormatterInterface;
use Scheb\Tombstone\Formatter\LogFormatter;
use Scheb\Tombstone\Vampire;

class AnalyzerLogHandler extends AbstractHandler
{
    const LOG_FILE_NAME = '%s-%s.tombstone';

    /**
     * @var StreamHandler[]
     */
    private $logStreams = array();

    /**
     * @var string
     */
    private $logDir;

    /**
     * @var int|null
     */
    private $sizeLimit;
    /**
     * @var null
     */
    private $filePermission;
    /**
     * @var bool
     */
    private $useLocking;

    /**
     * @param string $logDir
     * @param int|null $sizeLimit
     * @param null $filePermission
     * @param bool $useLocking
     */
    public function __construct($logDir, $sizeLimit = null, $filePermission = null, $useLocking = false)
    {
        $this->logDir = $logDir;
        $this->sizeLimit = $sizeLimit;
        $this->filePermission = $filePermission;
        $this->useLocking = $useLocking;
    }

    public function __destruct()
    {
        parent::__destruct();
        foreach ($this->logStreams as $stream) {
            $stream->close();
        }
    }

    /**
     * Log a vampire
     *
     * @param Vampire $vampire
     */
    public function log(Vampire $vampire)
    {
        $logFile = $this->getLogFile($vampire);
        if (!$this->sizeLimitReached($logFile)) {
            $logger = $this->getLogStream($logFile);
            $logger->log($vampire);
        }
    }

    /**
     * @param Vampire $vampire
     *
     * @return string
     */
    private function getLogFile(Vampire $vampire)
    {
        $date = date('Ymd');
        $hash = $vampire->getTombstone()->getHash();
        return $this->logDir . '/' . sprintf(self::LOG_FILE_NAME, $hash, $date);
    }

    /**
     * @param string $logFile
     *
     * @return StreamHandler
     */
    private function getLogStream($logFile)
    {
        if (!isset($this->logStreams[$logFile])) {
            $handler = new StreamHandler($logFile, $this->filePermission, $this->useLocking);
            $handler->setFormatter($this->getFormatter());
            $this->logStreams[$logFile] = $handler;
        }

        return $this->logStreams[$logFile];
    }

    /**
     * @param string $logFile
     *
     * @return bool
     */
    private function sizeLimitReached($logFile)
    {
        if ($this->sizeLimit <= 0 || !file_exists($logFile)) {
            return false;
        }

        clearstatcache(null, $logFile);
        return filesize($logFile) >= $this->sizeLimit;
    }

    /**
     * Flush everything
     */
    public function flush()
    {
        foreach ($this->logStreams as $stream) {
            $stream->flush();
        }
    }

    /**
     * Sets the formatter.
     *
     * @param  FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        throw new \LogicException('Formatter of AnalyzerLogHandler cannot be changed.');
    }

    /**
     * Gets the formatter.
     *
     * @return FormatterInterface
     */
    protected function getDefaultFormatter()
    {
        return new LogFormatter();
    }
}
