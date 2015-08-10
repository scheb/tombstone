<?php
namespace Scheb\Tombstone;

use Scheb\Tombstone\Handlers\HandlerInterface;
use Scheb\Tombstone\Tracing\TraceProvider;
use Scheb\Tombstone\Tracing\TraceProviderInterface;

class Tombstone {

    /**
     * @var TraceProviderInterface
     */
    private $traceProvider;

    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @param HandlerInterface[] $handlers
     */
    public function __construct(array $handlers = array()) {
        $this->handlers = $handlers;
        $this->traceProvider = new TraceProvider(1);
    }

    /**
     * @param TraceProviderInterface $traceProvider
     */
    public function setTraceProvider(TraceProviderInterface $traceProvider){
        $this->traceProvider = $traceProvider;
    }

    /**
     * @param HandlerInterface $handler
     */
    public function addHandler(HandlerInterface $handler) {
        $this->handlers[] = $handler;
    }

    /**
     * @param string $date
     * @param string $author
     */
    public function register($date, $author) {
        $trace = $this->traceProvider->getTrace();
        $vampire = Vampire::create($date, $author, $trace);
        foreach ($this->handlers as $handler) {
            $handler->log($vampire);
        }
    }

    public function flush() {
        foreach ($this->handlers as $handler) {
            $handler->flush();
        }
    }
}
