<?php

namespace Scheb\Tombstone;

class GraveyardProvider
{
    /**
     * @var Graveyard
     */
    private static $graveyard;

    public static function getGraveyard(): Graveyard
    {
        if (null === self::$graveyard) {
            self::$graveyard = new Graveyard();
        }

        return self::$graveyard;
    }

    public static function setGraveyard(Graveyard $graveyard): void
    {
        self::$graveyard = $graveyard;
    }
}
