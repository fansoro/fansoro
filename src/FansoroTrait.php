<?php
namespace Fansoro;

trait FansoroTrait
{
    /**
     * @var Fansoro
     */
    protected static $fansoro;

    /**
     * @return Fansoro
     */
    public static function getFansoro()
    {
        if (!self::$fansoro) {
            self::$fansoro = Fansoro::instance();
        }

        return self::$fansoro;
    }


    /**
     * @param Fansoro $fansoro
     */
    public static function setFansoro(Fansoro $fansoro)
    {
        self::$fansoro = $fansoro;
    }
}
