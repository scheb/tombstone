<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Graveyard;

use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Logger\Handler\HandlerInterface;

class GraveyardBuilder
{
    /**
     * @var int
     */
    private $stackTraceDepth = 0;

    /**
     * @var string|null
     */
    private $rootDir = null;

    /**
     * @var HandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var LoggerInterface|null
     */
    private $logger = null;

    /**
     * @var bool
     */
    private $buffered = false;

    /**
     * @var bool
     */
    private $autoRegister = false;

    /**
     * How many frames of the stack trace should be logged (default: 0).
     */
    public function stackTraceDepth(int $stackTraceDepth): self
    {
        $this->stackTraceDepth = $stackTraceDepth;

        return $this;
    }

    /**
     * Root dir of the project. File paths will be logged relative to that directory (if possible).
     */
    public function rootDir(?string $rootDir): self
    {
        $this->rootDir = $rootDir;

        return $this;
    }

    /**
     * Add a handler to the graveyard to log tombstone calls.
     */
    public function withHandler(HandlerInterface $handler): self
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * Add a PSR logger to the graveyard to log exceptions.
     */
    public function withLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Make it a buffered graveyard, that doesn't log tombstone calls immediately. The flush() method has to be called
     * to call the handlers.
     */
    public function buffered(): self
    {
        $this->buffered = true;

        return $this;
    }

    /**
     * Automatically register the new graveyard in the GraveyardRegistry once it is built.
     */
    public function autoRegister(): self
    {
        $this->autoRegister = true;

        return $this;
    }

    /**
     * Build and return the graveyard object.
     */
    public function build(): GraveyardInterface
    {
        $graveyard = new Graveyard(
            $this->buildVampireFactory(),
            $this->logger,
            $this->handlers
        );

        if ($this->buffered) {
            $graveyard = new BufferedGraveyard($graveyard);
        }

        if ($this->autoRegister) {
            GraveyardRegistry::setGraveyard($graveyard);
        }

        return $graveyard;
    }

    private function buildVampireFactory(): VampireFactory
    {
        return new VampireFactory($this->rootDir, $this->stackTraceDepth);
    }
}
