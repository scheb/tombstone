<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const CONFIG_ROOT = '';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_ROOT);
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            /** @psalm-suppress UndefinedMethod */
            $rootNode = $treeBuilder->root(self::CONFIG_ROOT);
        }

        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress PossiblyUndefinedMethod
         */
        $rootNode
            ->ignoreExtraKeys(false)
            ->children()
                ->arrayNode('source_code')
                    ->isRequired()
                    ->children()
                        ->scalarNode('root_directory')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->validate()
                                ->ifTrue($this->isNoDirectory())
                                ->thenInvalid('Must be a valid directory path, given: %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('tombstones')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('parser')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('excludes')
                                    ->scalarPrototype()->end()
                                ->end()
                                ->arrayNode('names')
                                    ->defaultValue(['*.php'])
                                ->scalarPrototype()->end()
                                ->end()
                                ->arrayNode('not_names')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('logs')
                    ->isRequired()
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
                                    ->validate()
                                        ->ifTrue($this->isNoFile())
                                        ->thenInvalid('Must be a valid file path, given: %s')
                                    ->end()
                                ->end()
                                ->scalarNode('class')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
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
                        ->scalarNode('checkstyle')
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
        return function (string $path): bool {
            $path = realpath($path);

            return !(false !== $path && is_file($path));
        };
    }

    private function isNoDirectory(): callable
    {
        return function (string $path): bool {
            $path = realpath($path);

            return !(false !== $path && is_dir($path));
        };
    }

    private function isNotWritableFile(): callable
    {
        return function (string $path): bool {
            $fileRealPath = realpath($path);
            $fileDirectory = realpath(\dirname($path));

            return !(
                (false !== $fileDirectory && is_writeable($fileDirectory)) // Path is within a writable directory
                && (false === $fileRealPath || (!is_dir($fileRealPath) && is_writeable($fileRealPath))) // Path is a writable file or non-existent
            );
        };
    }

    private function isNoWritableDirectory(): callable
    {
        return function (string $path): bool {
            $path = realpath($path);

            return !(false !== $path && is_dir($path) && is_writeable($path));
        };
    }
}
