<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Vampire;

/*
 * Based on the StreamHandler from Monolog
 * https://github.com/Seldaek/monolog
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 */
class StreamHandler extends AbstractHandler
{
    /**
     * @var resource|closed-resource|null
     */
    protected $stream;

    /**
     * @var string|null
     */
    protected $url;

    /**
     * @var string|null
     */
    private $errorMessage;

    /**
     * @var int|null
     */
    protected $filePermission;

    /**
     * @var bool
     */
    protected $useLocking;

    /**
     * @var bool
     */
    private $dirCreated = false;

    /**
     * @param resource|string $stream
     * @param int|null        $filePermission Optional file permissions (default (0644) are only for owner read/write)
     * @param bool            $useLocking     Try to lock log file before doing any writes
     *
     * @throws \Exception                If a missing directory is not buildable
     * @throws \InvalidArgumentException If stream is not a resource or string
     */
    public function __construct($stream, ?int $filePermission = null, $useLocking = false)
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (!\is_resource($stream) && !\is_string($stream)) {
            throw new \InvalidArgumentException('A stream must either be a resource or a string.');
        }

        if (\is_resource($stream)) {
            $this->stream = $stream;
        } else {
            $this->url = $stream;
        }

        $this->filePermission = $filePermission;
        $this->useLocking = $useLocking;
    }

    public function close(): void
    {
        if ($this->url && \is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
    }

    public function log(Vampire $vampire): void
    {
        $formatted = $this->getFormatter()->format($vampire);
        if (!\is_resource($this->stream)) {
            if (null === $this->url || '' === $this->url) {
                throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
            }
            $this->createDir();
            $this->errorMessage = null;
            /** @psalm-suppress InvalidArgument */
            set_error_handler([$this, 'customErrorHandler']);
            $this->stream = fopen($this->url, 'a');
            if (null !== $this->filePermission) {
                @chmod($this->url, $this->filePermission);
            }
            restore_error_handler();
            if (!\is_resource($this->stream)) {
                $this->stream = null;
                /** @psalm-suppress NullArgument */
                throw new \UnexpectedValueException(sprintf('The stream or file "%s" could not be opened: %s', $this->errorMessage, $this->url));
            }
        }
        if ($this->useLocking) {
            // ignoring errors here, there's not much we can do about them
            flock($this->stream, LOCK_EX);
        }
        fwrite($this->stream, $formatted);
        if ($this->useLocking) {
            flock($this->stream, LOCK_UN);
        }
    }

    private function customErrorHandler(int $code, string $msg): bool
    {
        $this->errorMessage = preg_replace('{^(fopen|mkdir)\(.*?\): }', '', $msg);

        return true;
    }

    private function getDirFromStream(string $stream): ?string
    {
        $pos = strpos($stream, '://');
        if (false === $pos) {
            return \dirname($stream);
        }
        if ('file://' === substr($stream, 0, 7)) {
            return \dirname(substr($stream, 7));
        }

        return null;
    }

    private function createDir(): void
    {
        // Do not try to create dir if it has already been tried.
        if ($this->dirCreated) {
            return;
        }
        /** @psalm-suppress PossiblyNullArgument */
        $dir = $this->getDirFromStream($this->url);
        if (null !== $dir && !is_dir($dir)) {
            $this->errorMessage = null;
            /** @psalm-suppress InvalidArgument */
            set_error_handler([$this, 'customErrorHandler']);
            $status = mkdir($dir, 0777, true);
            restore_error_handler();
            if (false === $status && !is_dir($dir)) {
                /** @psalm-suppress NullArgument */
                throw new \UnexpectedValueException(sprintf('There is no existing directory at "%s" and its not buildable: %s', $dir, $this->errorMessage));
            }
        }
        $this->dirCreated = true;
    }
}
