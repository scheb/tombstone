<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Graveyard;

class GraveyardRegistry
{
    /**
     * @var GraveyardInterface|null
     */
    private static $graveyard;

    public static function getGraveyard(): GraveyardInterface
    {
        if (null !== self::$graveyard) {
            return self::$graveyard;
        }

        throw new GraveyardNotSetException('A graveyard has not been set. Please create one with GraveyardBuilder and register it to GraveyardRegistry.');
    }

    public static function setGraveyard(GraveyardInterface $graveyard): void
    {
        self::$graveyard = $graveyard;
    }
}
