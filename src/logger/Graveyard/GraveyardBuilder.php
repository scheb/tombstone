<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Graveyard;

use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Core\Model\RootPath;
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
    private $rootDirectory = null;

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

    public function rootDirectory(string $rootDir): self
    {
        $this->rootDirectory = $rootDir;

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
        if (null === $this->rootDirectory) {
            throw new GraveyardBuilderException('You must configure a rootDirectory for your graveyard.');
        }

        return new VampireFactory(new RootPath($this->rootDirectory), $this->stackTraceDepth);
    }
}
