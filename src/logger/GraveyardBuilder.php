<?php

declare(strict_types=1);

namespace Scheb\Tombstone;

use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Handler\HandlerInterface;

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

    public function stackTraceDepth(int $stackTraceDepth): self
    {
        $this->stackTraceDepth = $stackTraceDepth;

        return $this;
    }

    public function rootDir(?string $rootDir): self
    {
        $this->rootDir = $rootDir;

        return $this;
    }

    public function withHandler(HandlerInterface $handler): self
    {
        $this->handlers[] = $handler;

        return $this;
    }

    public function withLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function buffered(): self
    {
        $this->buffered = true;

        return $this;
    }

    public function autoRegister(): self
    {
        $this->autoRegister = true;

        return $this;
    }

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
