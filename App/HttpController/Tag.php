<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Common\Lib\Factory;

class Tag extends Controller
{

    public function index ()
    {
        // 用户1的标签有篮球、热血少年、漫画、火影、詹姆斯
        $this->addTag(1, '篮球');
        $this->addTag(1, '热血少年');
        $this->addTag(1, '漫画');
        $this->addTag(1, '火影');
        $this->addTag(1, '詹姆斯');

        // 用户2的标签有篮球、游戏、lol、詹姆斯、纳达尔
        $this->addTag(2, '篮球');
        $this->addTag(2, '游戏');
        $this->addTag(2, 'lol');
        $this->addTag(2, '詹姆斯');
        $this->addTag(2, '纳达尔');

        // 用户3的标签有
        $this->addTag(3, '篮球');
        $this->addTag(3, '音乐');
        $this->addTag(3, 'IT');
        $this->addTag(3, '游戏');
        $this->addTag(3, '电影');

        // 查看用户1和用户2共同标签
        $inter = Factory::getRedis()->sInter("user:1:tag", "user:2:tag");
        $this->response()->withHeader('Content-type','text/html;charset=utf-8');
        $this->response()->write(print_r($inter, true));
        // Array ( [0] => 篮球 [1] => 詹姆斯 )

        // 查看同时含有篮球、游戏的用户
        $inter = Factory::getRedis()->sInter("篮球:users", "游戏:users");
        $this->response()->write(print_r($inter, true));
        // Array ( [0] => 2 [1] => 3 )
    }

    public function addTag ($userId, $tag)
    {
        $redis = Factory::getRedis();
        $userkey = "user:$userId:tag";
        $tagKey = "$tag:users";

        $redis->sAdd($userkey, $tag);
        $redis->sAdd($tagKey, $userId);
    }

    public function delTAg ($userId, $tag)
    {
        $redis = Factory::getRedis();
        $userkey = "user:$userId:tag";
        $tagKey = "$tag:users";

        $redis->sRem($userkey, $tag);
        $redis->sRem($tagKey, $userId);
    }
}