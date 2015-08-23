<?php
namespace Scheb\Tombstone;

use Scheb\Tombstone\Handler\HandlerInterface;
use Scheb\Tombstone\Tracing\RelativePath;
use Scheb\Tombstone\Tracing\TraceProvider;

class Graveyard
{

    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @var string
     */
    private $sourceDir;

    /**
     * @param HandlerInterface[] $handlers
     * @param null $sourceDir
     */
    public function __construct(array $handlers = array(), $sourceDir = null)
    {
        $this->handlers = $handlers;
        $this->traceProvider = new TraceProvider(1);
		$this->setSourceDir($sourceDir);
    }

    /**
     * @param string $sourceDir
     */
    public function setSourceDir($sourceDir)
    {
        $this->sourceDir = str_replace('\\', '/', $sourceDir);
    }

    /**
     * @param HandlerInterface $handler
     */
    public function addHandler(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param string $date
     * @param string $author
     * @param string|null $label
     * @param array $trace
     */
    public function tombstone($date, $author, $label, array $trace)
    {
        $trace = $this->traceRelativePath($trace);
        $vampire = Vampire::createFromCall($date, $author, $label, $trace);
        foreach ($this->handlers as $handler) {
            $handler->log($vampire);
        }
    }

    /**
     * @param array $trace
     *
     * @return array
     */
    private function traceRelativePath(array $trace)
    {
        if (!$this->sourceDir) {
            return $trace;
        }

        foreach ($trace as $key => &$frame) {
            if (isset($frame['file'])) {
                $frame['file'] = str_replace('\\', '/', $frame['file']);
                $frame['file'] = RelativePath::makeRelativeTo($frame['file'], $this->sourceDir);
            }
        }

        return $trace;
    }

    public function flush()
    {
        foreach ($this->handlers as $handler) {
            $handler->flush();
        }
    }
}
