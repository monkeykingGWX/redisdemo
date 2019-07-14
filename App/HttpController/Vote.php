<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Common\Lib\Factory;

class Vote extends Controller
{
    /**
     * 模拟投票
     * 每个用户每天有10次投票机会
     */
    public function index ()
    {
        $voteTimerKey = "test:vote:timer";

        // 开启定时器，消费者消费队列
        if (!Factory::getRedis()->get($voteTimerKey)) {
            $timerId = $this->timer();
            Factory::getRedis()->set($voteTimerKey, $timerId);
        }

        // 投票业务逻辑
        for ($i = 1; $i <= 10000; $i++) {
            $uid   = mt_rand(1, 1000);
            $toUid = mt_rand(1, 1000);
            $time  = mktime(mt_rand(0, 23), mt_rand(0, 59), mt_rand(0, 59));

            if ($uid == $toUid) {
                continue;     // 自己不能给自己投票
            }

            if ($this->voteCnt($uid) > 10) {
                continue;    // 每人每天只能投10票
            }

            // 投票结果记录在redis列表中
            $voteKey = "test:vote";
            $voteVal = $uid . ':' . $toUid . ':' . $time;
            Factory::getRedis()->lpush($voteKey, $voteVal);
        }
    }

    /**
     * 每人每日投票数
     * @param $uid
     * @return float
     */
    private function voteCnt ($uid)
    {
        $key = 'test:votecnt';
        $redis = Factory::getRedis();

        $redis->zIncrBy($key, 1, $uid);
        $count = $redis->zScore($key, $uid);

        if ($redis->ttl($key) == -1) {
            $timestamp = mktime(23, 59, 59);
            $redis->expireAt($key, $timestamp);
        }

        return $count;
    }

    /**
     * 定时器，每秒执行1次，每次在队列中取10000条数据
     * 将投票结果持久化到mysql中
     * @return int
     */
    private function timer ()
    {
        $timer   = \EasySwoole\Component\Timer::getInstance();
        $timeId  = $timer->loop(1 * 1000, function () {
            $sql     = "INSERT INTO vote(uid,touid,add_time) VALUES";
            $redis   = Factory::getRedis();
            $mysql   = Factory::getMysqli();
            $listKey = "test:vote";

            for ($i = 0; $i < 10000; $i++) {
                if (!$redis->lLen($listKey)) {
                    break;
                }

                $voteInfo = $redis->rpop($listKey);
                $tmpArr = explode(":", $voteInfo);
                $sql .= "('{$tmpArr[0]}','{$tmpArr[1]}','{$tmpArr[2]}'),";
            }

            if ($i) {
                $mysql->rawQuery(rtrim($sql, ','));
            }

        });

        return $timeId;
    }

    /**
     * 清空定时器
     */
    public function clearTimer ()
    {
        $voteTimerKey = "test:vote:timer";
        $timeId       = Factory::getRedis()->get($voteTimerKey);

        if ($timeId) {
            \EasySwoole\Component\Timer::getInstance()->clear($timeId);
            Factory::getRedis()->delete($voteTimerKey);
        } else {
            echo "no timer run\n";
        }
    }
}