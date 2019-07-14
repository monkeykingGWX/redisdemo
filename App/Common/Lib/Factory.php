<?php
namespace App\Common\Lib;

use EasySwoole\EasySwoole\Config;

class Factory
{
    public static function getMysqli ()
    {
        $alias = 'mysqli';

        $mysqli = Register::get($alias);

        if (!$mysqli) {
            $mysqlConf = new \EasySwoole\Mysqli\Config(Config::getInstance()->getConf('MYSQL'));
            $mysqli = new \EasySwoole\Mysqli\Mysqli($mysqlConf);
            Register::set($alias, $mysqli);
        }

        return $mysqli;
    }

    public static function getCoRedis ()
    {
        $redis = new \Swoole\Coroutine\Redis();
        $confInstance = Config::getInstance();
        $redis->connect($confInstance->getConf('REDIS.host'), $confInstance->getConf('REDIS.port'));

        return $redis;
    }

    public static function getRedis ()
    {
        $alias = 'redis';

        $redis = Register::get($alias);

        if (!$redis) {
            $redisConf = self::getEasyConfig("REDIS");
            $redis = new \Redis();
            $redis->connect($redisConf['host'], $redisConf['port']);
            $redis->select(1);
            Register::set($alias, $redis);
        }

        return $redis;
    }

    public static function getCoMysql ()
    {
        $confInstance = Config::getInstance();
        $swoole_mysql = new \Swoole\Coroutine\MySQL();
        $swoole_mysql->connect($confInstance->getConf('MYSQL'));

        return $swoole_mysql;
    }

    public static function getEasyConfig (string $confStr = '')
    {
        return Config::getInstance()->getConf($confStr);
    }
}