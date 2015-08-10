<?php
namespace Scheb\Tombstone\Handlers;

use Scheb\Tombstone\Formatters\FormatterInterface;
use Scheb\Tombstone\Vampire;

interface HandlerInterface {

    /**
     * Log a vampire
     *
     * @param Vampire $vampire
     */
    public function log(Vampire $vampire);

    /**
     * Flush everything
     */
    public function flush();

    /**
     * Sets the formatter.
     *
     * @param  FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter);

    /**
     * Gets the formatter.
     *
     * @return FormatterInterface
     */
    public function getFormatter();
}
