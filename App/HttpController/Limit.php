<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Common\Lib\Factory;

class Limit extends Controller
{

    /**
     * 1分钟内一个ip只能访问5次
     */
    function index()
    {
        $clientIp = $this->request()->getServerParams()['remote_addr'];

        $redis = Factory::getRedis();
        $key = "test:$clientIp";

        $isExists = $redis->set($key, 1, ['nx', 'ex'=>60]);

        if ($isExists || $redis->incr($key) <=5) {
            $this->response()->write('ok');
        } else {
            $this->response()->write('not ok');
        }
    }
}
