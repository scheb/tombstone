<?php

namespace Scheb\Tombstone\Analyzer\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const CONFIG_ROOT = 'root';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_ROOT);
        $rootNode = $treeBuilder->getRootNode();

        /* @var ArrayNodeDefinition $rootNode */
        $rootNode
            ->ignoreExtraKeys(false)
            ->children()
                ->arrayNode('source')
                    ->children()
                        ->arrayNode('directories')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->scalarPrototype()
                                ->validate()
                                    ->ifTrue($this->isNoDirectory())
                                    ->thenInvalid('Must be a valid directory path, given: %s')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('excludes')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('names')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('notNames')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('rootDir')
                    ->isRequired()
                    ->validate()
                        ->ifTrue($this->isNoDirectory())
                        ->thenInvalid('Must be a valid directory path, given: %s')
                    ->end()
                ->end()
                ->arrayNode('logs')
                    ->children()
                        ->scalarNode('directory')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->validate()
                                ->ifTrue($this->isNoDirectory())
                                ->thenInvalid('Must be a valid directory path, given: %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('report')
                    ->children()
                        ->scalarNode('php')
                            ->defaultNull()
                            ->validate()
                                ->ifTrue($this->isNotWritableFile())
                                ->thenInvalid('Must be a writable file path, given: %s')
                            ->end()
                        ->end()
                        ->scalarNode('html')
                            ->defaultNull()
                            ->validate()
                                ->ifTrue($this->isNoWritableDirectory())
                                ->thenInvalid('Must be a writable directory, given: %s')
                            ->end()
                        ->end()
                        ->scalarNode('console')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function isNoDirectory(): callable
    {
        return function ($path): bool {
            $path = realpath($path);

            return !(false !== $path && is_dir($path));
        };
    }

    private function isNotWritableFile(): callable
    {
        return function ($path): bool {
            $fileRealPath = realpath($path);
            $fileDirectory = realpath(dirname($path));

            return !(
                (false !== $fileDirectory && is_writeable($fileDirectory)) // Path is within a writable directory
                && (false === $fileRealPath || (!is_dir($fileRealPath) && is_writeable($fileRealPath))) // Path is a writable file or non-existent
            );
        };
    }

    private function isNoWritableDirectory(): callable
    {
        return function ($path): bool {
            $path = realpath($path);

            return !(false !== $path && is_dir($path) && is_writeable($path));
        };
    }
}
