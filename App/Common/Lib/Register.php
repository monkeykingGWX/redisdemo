<?php

namespace App\Common\Lib;

class Register
{
    private static $objects = [];

    public static function set ($alias, $object)
    {
        self::$objects[$alias] = $object;
    }

    public static function get ($alias)
    {
        return self::$objects[$alias] ?? null;
    }

    public static function unset($alias)
    {
        unset(self::$objects[$alias]);
    }
}