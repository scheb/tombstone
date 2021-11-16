<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core;

use Symfony\Component\Finder\Finder;

class FinderFacade
{
    private $items = [];

    private $excludes = [];

    private $names = [];

    private $notNames = [];

    public function __construct(array $items = [], array $excludes = [], array $names = [], array $notNames = [])
    {
        $this->items = $items;
        $this->excludes = $excludes;
        $this->names = $names;
        $this->notNames = $notNames;
    }

    /**
     * @return string[]
     */
    public function findFiles(): array
    {
        $files = [];
        $finder = new Finder();
        $iterate = false;

        $finder->ignoreUnreadableDirs();
        $finder->sortByName();

        foreach ($this->items as $item) {
            if (!is_file($item)) {
                $finder->in($item);
                $iterate = true;
            } else {
                $files[] = realpath($item);
            }
        }

        foreach ($this->excludes as $exclude) {
            $finder->exclude($exclude);
        }

        foreach ($this->names as $name) {
            $finder->name($name);
        }

        foreach ($this->notNames as $notName) {
            $finder->notName($notName);
        }

        if ($iterate) {
            foreach ($finder as $file) {
                $files[] = $file->getRealpath();
            }
        }

        return $files;
    }
}
