<?php
namespace Scheb\Tombstone\Handlers;

use Scheb\Tombstone\Formatters\FormatterInterface;
use Scheb\Tombstone\Formatters\LineFormatter;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var FormatterInterface
     */
    protected $formatter;

    public function __destruct()
    {
        try {
            $this->flush();
        } catch (\Exception $e) {
            // do nothing
        }
    }

    public function flush()
    {
    }

    /**
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }

        return $this->formatter;
    }

    /**
     * Gets the default formatter.
     *
     * @return FormatterInterface
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter();
    }
}
