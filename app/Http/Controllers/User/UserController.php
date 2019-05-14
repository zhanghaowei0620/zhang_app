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







//        $e = UserModel::where('email',$email)->first();
////        if($e){
////            $response = [
////                'error' => 50002,
////                'msg'   => '该邮箱已被注册'
////            ];
////            die(json_encode($response,JSON_UNESCAPED_UNICODE));
////        }
////        $n = UserModel::where('name',$name)->first();
////        if($n){
////            $response = [
////                'error' => 50003,
////                'msg'   => '该用户名已被注册'
////            ];
////            die(json_encode($response,JSON_UNESCAPED_UNICODE));
////        }
////
////        $pass = password_hash($user_pwd,PASSWORD_BCRYPT);
////        //var_dump($hash);
////        $data = [
////            'name'=>$name,
////            'email'=>$email,
////            'password'=>$pass,
////            'add_time'=>time()
////        ];
////        $res = UserModel::insertGetId($data);
////        if($res){
////            $response = [
////                'error'=>0,
////                'msg'=>'注册成功'
////            ];
////        }
////        die(json_encode($response,JSON_UNESCAPED_UNICODE));
    }

    /**登陆*/
    public function login(){
        return view('user.login');
    }
    public function logindo(Request $request){
        $email = $request->input('email');
        $password = $request->input('user_pwd');

        $passInfo = DB::table('user_info')->where('email',$email)->first();
        //var_dump($passInfo);exit;
        if(empty($email)){
            $response = [
                'error'=>50004,
                'msg'=>'邮箱不能为空'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($password)){
            $response = [
                'error'=>50005,
                'msg'=>'请填写正确的密码'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($passInfo)){
            $response = [
                'error'=>50005,
                'msg'=>'请输入正确的email或密码'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $pass = $passInfo->password;
        $uid = $passInfo->uid;
        //var_dump($pass);exit;
        $user_pass = password_verify($password,$pass);
        //var_dump($user_pass);exit;
        if($user_pass == false){
            $response = [
                'error'=>50005,
                'msg'=>'请输入正确的email或密码'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }else{
            $token = sha1(Str::random(10).md5(time()).$uid);
            $response = [
                'error'=>0,
                'msg'=>'登陆成功',
                'token'=>$token
            ];

            $id = Redis::incr('id');
            $hsetkey = "id_{$id}";
            $keylist = "H:user_login";
            Redis::hset($hsetkey,'id',$id);
            Redis::hset($hsetkey,'user_id',$uid);
            Redis::hset($hsetkey,'token',$token);
            Redis::hset($hsetkey,'createtime',time());
            Redis::lpush($keylist,$hsetkey);


            //var_dump($token);exit;

            if($email=='zhaoda@qq.com' && $password==$user_pass){

            }

        }


        die(json_encode($response,JSON_UNESCAPED_UNICODE));

        //var_dump($data);exit;

    }
}
