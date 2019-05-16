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
        $data = file_get_contents('php://input');
        $data_info = base64_decode($data);
        $public_key = openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($data_info,$dec_data,$public_key);

        $info = json_decode($dec_data,true);
        $name = $info['name'];
        $email = $info['email'];
        $user_pwd = $info['user_pwd'];
        if(empty($name)){
            $response = [
                'error' => 50005,
                'msg'   => '账号不能为空'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($email)){
            $response = [
                'error' => 50006,
                'msg'   => '邮箱不能为空'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($user_pwd)){
            $response = [
                'error' => 50005,
                'msg'   => '请输入密码'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }

        $e = DB::table('user_info')->where('email',$email)->first();
        if($e){
            $response = [
                'error' => 50002,
                'msg'   => '该邮箱已被注册'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $n = DB::table('user_info')->where('name',$name)->first();
        if($n){
            $response = [
                'error' => 50003,
                'msg'   => '该用户名已被注册'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }

        $pass = password_hash($user_pwd,PASSWORD_BCRYPT);
//var_dump($hash);
        $data = [
            'name'=>$name,
            'email'=>$email,
            'user_pwd'=>$pass,
            'add_time'=>time()
        ];
        $res = DB::table('user_info')->insertGetId($data);
        if($res){
            $response = [
                'error'=>0,
                'msg'=>'注册成功'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
    }

    /**登陆*/
    public function login(){
        return view('user.login');
    }

    public function logindo(){
        $data = file_get_contents('php://input');
        $info = json_decode($data,true);
        $email = $info['email'];
        $user_pwd = $info['user_pwd'];
        $passInfo = DB::table('user_info')->where('email',$email)->first();
        if(empty($email)){
            $response = [
                'error'=>50004,
                'msg'=>'邮箱不能为空'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($user_pwd)){
            $response = [
                'error'=>50005,
                'msg'=>'密码不能为空'
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
        $pass = $passInfo->user_pwd;
        $uid = $passInfo->uid;
        //var_dump($pass);exit;
        $user_pass = password_verify($user_pwd,$pass);
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


            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }

    }
}
