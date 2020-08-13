<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Config;

use Scheb\Tombstone\Analyzer\Config\YamlConfigProvider;
use Scheb\Tombstone\Tests\TestCase;

class YamlConfigProviderTest extends TestCase
{
    private const CONFIG_DIR = __DIR__.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR;

    protected function readConfiguration(string $file): array
    {
        $configProvider = new YamlConfigProvider(self::CONFIG_DIR.$file);

        return $configProvider->readConfiguration();
    }

    /**
     * @test
     */
    public function processConfiguration_minimum_haveDirectoriesSet(): void
    {
        $config = $this->readConfiguration('minimum.yml');

        $expectedConfig = [
            'source_code' => [
                'root_directory' => self::CONFIG_DIR.'src',
            ],
            'logs' => [
                'directory' => self::CONFIG_DIR.'logs',
            ],
        ];

        $this->assertEquals($expectedConfig, $config);
    }

    /**
     * @test
     */
    public function processConfiguration_fullConfig_haveAllValuesSet(): void
    {
        $config = $this->readConfiguration('full.yml');

        $expectedConfig = [
            'source_code' => [
                'root_directory' => self::CONFIG_DIR.'src',
                'excludes' => [
                    'tests',
                ],
                'names' => [
                    '*.php',
                ],
                'not_names' => [
                    '*.js',
                ],
            ],
            'logs' => [
                'directory' => self::CONFIG_DIR.'logs',
            ],
            'report' => [
                'php' => self::CONFIG_DIR.'report'.DIRECTORY_SEPARATOR.'tombstone-report.php',
                'checkstyle' => self::CONFIG_DIR.'report'.DIRECTORY_SEPARATOR.'checkstyle.xml',
                'html' => self::CONFIG_DIR.'report',
                'console' => true,
            ],
        ];

        $this->assertEquals($expectedConfig, $config);
    }
}
