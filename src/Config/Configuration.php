<?php

declare(strict_types=1);

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
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root(self::CONFIG_ROOT);
        }

        /* @var ArrayNodeDefinition $rootNode */
        $rootNode
            ->ignoreExtraKeys(false)
            ->children()
                ->arrayNode('source')
                    ->isRequired()
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
                            ->defaultValue(['*.php'])
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('notNames')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('rootDir')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue($this->isNoDirectory())
                        ->thenInvalid('Must be a valid directory path, given: %s')
                    ->end()
                ->end()
                ->arrayNode('logs')
                    ->isRequired()
                    ->validate()
                        ->ifEmpty()
                        ->thenInvalid('Must have at least one log provider configured')
                    ->end()
                    ->children()
                        ->scalarNode('directory')
                            ->cannotBeEmpty()
                            ->validate()
                                ->ifTrue($this->isNoDirectory())
                                ->thenInvalid('Must be a valid directory path, given: %s')
                            ->end()
                        ->end()
                        ->arrayNode('custom')
                            ->children()
                                ->scalarNode('file')
                                    ->isRequired()
                                    ->validate()
                                        ->ifTrue($this->isNoFile())
                                        ->thenInvalid('Must be a valid file path, given: %s')
                                    ->end()
                                ->end()
                                ->scalarNode('class')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('report')
                    ->addDefaultsIfNotSet()
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
                        ->scalarNode('console')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function isNoFile(): callable
    {
        return function ($path): bool {
            $path = realpath($path);

            return !(false !== $path && !is_dir($path));
        };
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
