<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Model\UserModel;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**注册*/
    public function register(){
        return view('user.register');
    }
    public function registerAdd(Request $request){
        $name = $request->input('name');
        $email = $request->input('email');
        $user_pwd = $request->input('user_pwd');
        $user_pwd1 = $request->input('user_pwd1');
        if($user_pwd != $user_pwd1){
            $response = [
                'error'=>50001,
                'msg'=>'两次输入密码不相同'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $data = [
            'name'=>$name,
            'email'=>$email,
            'user_pwd'=>$user_pwd
        ];
        $json_str = json_encode($data,JSON_UNESCAPED_UNICODE);
        //加密
        $private = openssl_pkey_get_private('file://'.storage_path('app/keys/private.pem'));
        openssl_private_encrypt($json_str,$enc_data,$private);
        $enc_data = base64_encode($enc_data);
        //var_dump($enc_data);
        $url = "http://lumen.1809a.com/reg";
        //var_dump($url);exit;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$enc_data);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type:text/plain']);

        curl_exec($ch);
        //$code= curl_errno($ch);
        //var_dump($code);exit;
        curl_close($ch);
    }

    /**登陆*/
    public function login(){
        return view('user.login');
    }
}
