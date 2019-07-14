<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Common\Lib\Factory;

class Cache extends Controller
{
    public function index ()
    {
        $uid    = (int) $this->request()->getRequestParam('uid');
        $userinfo = $this->getUserInfo($uid);
        $this->response()->write(print_r($userinfo, true));
    }

    /**
     * redis实现缓存功能
     * 缓存时间1小时
     * @param $uid
     * @return bool|\EasySwoole\Mysqli\Mysqli|mixed|null|string
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    private function getUserInfo ($uid)
    {
        $mysqli = Factory::getMysqli();
        $redis  = Factory::getRedis();

        $key      = "test:user:". $uid;
        $userinfo = $redis->get($key);

        if (!$userinfo) {
            $userinfo = $mysqli->where("user_id", $uid)->getOne("user");

            if ($userinfo) {
                $redis->set($key, json_encode($userinfo), 3600);
            }
        } else {
            $userinfo = json_decode($userinfo, true);
        }

        return $userinfo;
    }
}