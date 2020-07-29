<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Cli;

use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wrap Symfony's ProgressBar class to make it mockable.
 */
class ProgressBar
{
    /**
     * @var SymfonyProgressBar
     */
    private $progressBar;

    public function __construct(OutputInterface $output, int $width)
    {
        $this->progressBar = new SymfonyProgressBar($output, $width);
        $this->progressBar->setBarWidth(50);
        $this->progressBar->display();
    }

    public function advance(): void
    {
        $this->progressBar->advance();
    }
}
