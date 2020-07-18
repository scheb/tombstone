<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Cli;

use Symfony\Component\Console\Application as AbstractApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends AbstractApplication
{
    public function __construct()
    {
        parent::__construct('cli', '');
    }

    protected function getCommandName(InputInterface $input)
    {
        return 'tombstone';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = AbstractApplication::getDefaultCommands();
        $defaultCommands[] = new AnalyzeCommand();

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = AbstractApplication::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Tombstone Analyzer');
        AbstractApplication::doRun($input, $output);

        return 0;
    }
}
