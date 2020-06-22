<?php

declare(strict_types=1);

namespace Scheb\Tombstone;

class GraveyardProvider
{
    /**
     * @var GraveyardInterface
     */
    private static $graveyard;

    public static function getGraveyard(): GraveyardInterface
    {
        if (null === self::$graveyard) {
            self::$graveyard = new Graveyard();
        }

        return self::$graveyard;
    }

    public static function setGraveyard(GraveyardInterface $graveyard): void
    {
        self::$graveyard = $graveyard;
    }
}
