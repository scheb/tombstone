<?php

namespace Scheb\Tombstone\Analyzer\Cli;

use Symfony\Component\Console\Application as AbstractApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends AbstractApplication
{
    const VERSION = 'dev-master';

    public function __construct()
    {
        AbstractApplication::__construct('cli', self::VERSION);
    }

    protected function getCommandName(InputInterface $input)
    {
        return 'tombstone';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = AbstractApplication::getDefaultCommands();
        $defaultCommands[] = new Command();

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = AbstractApplication::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Tombstone Analyzer '.$this->getVersion());
        if (!$input->getFirstArgument()) {
            $input = new ArrayInput(['--help']);
        }
        AbstractApplication::doRun($input, $output);
    }
}
