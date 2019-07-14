<?php

namespace App\HttpController;

use App\Common\Lib\Factory;
use EasySwoole\Http\AbstractInterface\Controller;

class Lottery extends Controller
{
    const SAME       = 1;        // 抽奖算法1
    const DIFFIERENT = 2;        // 抽奖算法2
    const SADDNUM    = 1000;     // 每次sadd最多数

    public function index ()
    {
        $this->setTotal('lottery1', 100000);

        // 抽10次奖,每次概率都一样
        for($i=1; $i<=10; $i++) {
            $flag = $this->draw('lottery1', 10000);
            if ($flag) {
                $this->response()->write('bingo<br />');
            } else {
                $this->response()->write('not get<br />');
            }
        }

        // 抽10次奖，每次概率都不一样
        for($i=1; $i<=10; $i++) {
            $flag = $this->draw('lottery1', 10000, self::DIFFIERENT);
            if ($flag) {
                $this->response()->write('bingo<br />');
            } else {
                $this->response()->write('not get<br />');
            }
        }
    }


    /**
     * @param $count
     * @param $key
     */
    public function setTotal ($key, int $count)
    {
        $redis = Factory::getRedis();

        // 一次只sadd self::SADDNUM个数
        for ($i = 1; $i < $count; $i = $i + self::SADDNUM) {
            $redis->sAddArray($key, range($i, $i + self::SADDNUM -1));
        }
    }

    /**
     * 抽奖
     * 算法一、每次抽奖概率都一样
     *   概率 $bingo/$count
     * 算法二、每抽完一次概率都不一样
     *   概率 ($bingo-已抽中次数)/($count-已抽奖次数)
     * @param $key
     * @param $prob
     */
    public function draw (string $key, int $prob, $type = self::SAME)
    {
        $redis = Factory::getRedis();

        switch ($type) {
            case self::SAME :
                $num = $redis->sRandMember($key);
                break;

            case self::DIFFIERENT :
                $num = $redis->sPop($key);
                break;
        }

       return $num <= $prob;
    }
}