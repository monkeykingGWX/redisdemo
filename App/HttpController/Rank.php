<?php

namespace App\HttpController;

use App\Common\Lib\Factory;
use EasySwoole\Http\AbstractInterface\Controller;

class Rank extends Controller
{
    public function index ()
    {
        // 场景：一个视频系统，按浏览数排名
        // 分为今日排名，3日排名，一周排名，一月排名
    }

    public function viewVideo ()
    {
        $videoId = (int) $this->request()->getRequestParam('video_id');

        if (!$videoId) {
            return false;
        }

        $redis = Factory::getRedis();
        $key   = "video:rank:" . date('Y_m_d', time());

        if (!$redis->exists($key)) {
            // 键有效期为30天
            $redis->zIncrBy($key, 1, $videoId);
            $redis->expire($key, 86400 * 30);
        }

        $redis->zIncrBy($key, 1, $videoId);
    }

    /**
     * 模拟30天内视频观看
     */
    public function viewVideo2 ()
    {
        $videoId  = mt_rand(1, 1000);
        $leftDays = mt_rand(0, 29);
        $time     = time() - 86400 * $leftDays;
        $expire   = 86400 * (30-$leftDays);

        $redis = Factory::getRedis();
        $key   = "video:rank:" . date('Y_m_d', $time);

        if (!$redis->exists($key)) {
            $redis->zIncrBy($key, 1, $videoId);
            $redis->expire($key, $expire);
        }

        $redis->zIncrBy($key, 1, $videoId);
    }

    // 今日最热,合并今日与昨天的数据，防止今日数据为空白或很少情况
    public function hotToday ()
    {
        $redis = Factory::getRedis();
        $keyToday   = "video:rank:" . date('Y_m_d', time());
        $keyYes   = "video:rank:" . date('Y_m_d', time() - 86400);

        $keyUnion = "video:rank:today";
        $redis->zUnion($keyUnion, [$keyToday, $keyYes]);

        // 取前50名
        $ranks = $redis->zRevRange($keyUnion, 0, 9, true);
        $this->response()->write(print_r($ranks, true));

        /**
         *
        Array
        (
        [209] => 19  视频id => 观看次数
        [40] => 18
        [808] => 16
        [90] => 15
        [889] => 15
        [937] => 14
        [847] => 14
        [814] => 14
        [80] => 14
        [676] => 14
        )
         */
    }

    public function hotThree ()
    {
        $redis = Factory::getRedis();
        $unionKeys = [];

        for ($i = 0; $i < 3; $i++) {
            $key = "video:rank:" . date('Y_m_d', time() - 86400 * $i);
            $unionKeys[] = $key;
        }

        $keyUnion = "video:rank:three";
        $redis->zUnion($keyUnion, $unionKeys);

        // 取前50名
        $ranks = $redis->zRevRange($keyUnion, 0, 49, true);
        $this->response()->write(print_r($ranks, true));
    }

    public function hotWeek ()
    {
        $redis = Factory::getRedis();
        $unionKeys = [];

        for ($i = 0; $i < 7; $i++) {
            $key = "video:rank:" . date('Y_m_d', time() - 86400 * $i);
            $unionKeys[] = $key;
        }

        $keyUnion = "video:rank:week";
        $redis->zUnion($keyUnion, $unionKeys);

        // 取前50名
        $ranks = $redis->zRevRange($keyUnion, 0, 49, true);
        $this->response()->write(print_r($ranks, true));
    }

    public function hotMonth ()
    {
        $redis = Factory::getRedis();
        $unionKeys = [];

        for ($i = 0; $i < 30; $i++) {
            $key = "video:rank:" . date('Y_m_d', time() - 86400 * $i);
            $unionKeys[] = $key;
        }

        $keyUnion = "video:rank:month";
        $redis->zUnion($keyUnion, $unionKeys);

        // 取前50名
        $ranks = $redis->zRevRange($keyUnion, 0, 49, true);
        $this->response()->write(print_r($ranks, true));
    }
}