<?php

namespace Scheb\Tombstone;

class GraveyardProvider
{
    /**
     * @var Graveyard
     */
    private static $graveyard;

    /**
     * @return Graveyard
     */
    public static function getGraveyard()
    {
        if (null === self::$graveyard) {
            self::$graveyard = new Graveyard();
        }

        return self::$graveyard;
    }

    /**
     * @param Graveyard $graveyard
     */
    public static function setGraveyard(Graveyard $graveyard)
    {
        self::$graveyard = $graveyard;
    }
}
