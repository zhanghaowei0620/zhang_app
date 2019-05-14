<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;

class RequestTest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed请求超过限制
     */
    public function handle($request, Closure $next)
    {
        //print_r($_COOKIE);
        if(empty($_COOKIE['token']) || empty($_COOKIE['uid'])){
            header('Refresh:2;url=http://passport.1809a_api.com/api/login');
            die('请先登陆授权');
        }



        //echo date('Y-m-d H:i:s',time());'</br>';
        $ip = $_SERVER['SERVER_ADDR'];
        //var_dump($ip);exit;

        $key = 'request'.$ip;
        $num = Redis::get($key);
        //var_dump($num);exit;

        if($num>10){
            die('请求超过限制');
        }

        Redis::incr($key);
        Redis::expire($key,10);

        return $next($request);

    }
}


