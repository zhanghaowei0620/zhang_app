<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    public function insert10k(){
        for($i=0;$i<100;$i++){
            $user_name = mt_rand(5,10);
            $length = mt_rand(5,10);
            $e_num = mt_rand(0,3);
            $email = [
                '@qq.com',
                '@163.com',
                '@gmail.com',
                '@sohu.com'
            ];

            $u = [
                'user_name' =>Str::random($user_name),
                'email'=>Str::random($length) . $email[$e_num],
                'age'=>mt_rand(10,100),
                'reg_time'=>time()
            ];

            $uid = DB::table('users')->insertGetId($u);
            echo $uid;
        }
    }

    //分表
    public function p_user(){
        $data = DB::table('users')->get();
        //print_r($data);exit;
        foreach($data as $k => $v){
            $data1 = [];
            foreach ($v as $key=>$val) {
                $data1 = [
                    'uid'=>$v->uid,
                    'user_name'=>$v->user_name,
                    'email'=>$v->email,
                    'age'=>$v->age,
                    'reg_time' =>time()
                ];
            }
            $uid = $v->uid;
            //$uid = Redis::incr('incr:generate_uid');
            echo 'uid:' .$uid;echo '</br>';
            $table_id = $uid % 5;

            $table = 'p_users_'.$table_id;
            //echo $table;
            $res = DB::table($table)->insertGetId($data1);
        }

        //var_dump($user_name);exit;


    }
    /**分区*/
    public function partition(){
        $data = DB::table('users')->get()->toArray();
        //print_r($data);exit;
        foreach($data as $k => $v){
            foreach ($v as $key=>$val) {
                $data1 = [
                    'uid'=>$v->uid,
                    'user_name'=>$v->user_name,
                    'email'=>$v->email,
                    'age'=>$v->age,
                    'reg_time' =>time()
                ];
            }
            $table = 'par_user';

            $res = DB::table($table)->insertGetId($data1);
        }
    }

    public  function user_test(){
        $data = DB::table('users')->get()->toArray();
        //var_dump($data);exit;
        $data1 = [];
        foreach ($data as $k => $v) {


            foreach ($v as $key=>$val) {
                $data1 = [
                    'uid'=>$v->uid,
                    'user_name'=>$v->user_name,
                    'email'=>$v->email,
                    'age'=>$v->age,
                    'reg_time' =>time()
                ];
            }

            $uid = $v->uid;
            $table_id = $uid % 5;
            $table = 'p_users_'.$table_id;

            //var_dump($data1);exit;
            DB::table($table)->insertGetId($data1);
        }
        var_dump($uid);exit;
//        var_dump($data1);exit;


    }























    public function user_info(){
        $uid = Redis::incr('incr:user_id');
        //echo 'uid:' .$uid;echo '</br>';
        $table_id = $uid % 5;
        $table = 'p_user_'.$table_id;
        //echo $table;echo '</br>';
        $res = DB::table($table)->where('user_id',$uid)->get()->toArray();
        print_r($res);
    }


































































    /**对称加密*/
    public function test(Request $request){
        $data = [
            'uid'=>3,
            'name'=>'王五',
            'age'=>25
        ];
        $method = 'AES-256-CBC';
        $key = "abcdefg";
        $option = OPENSSL_RAW_DATA;
        $iv = 'djadjlajdlajdjkl';
        $jasn_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $enc_str = base64_encode(openssl_encrypt($jasn_data,$method,$key,$option,$iv));
//        var_dump($enc_str);
        $url = "http://lumen.1809a.com/test";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$enc_str);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type:text/plain']);

        curl_exec($ch);
        //$code= curl_errno($ch);
        //var_dump($code);exit;
        curl_close($ch);
//        return $info;
    }
    /**凯撒加密*/
    public function caesar($str,$n=3){
        $str = "hello world";
        $pass = "";
        $lenth = strlen($str);
        for($i=0;$i<$lenth;$i++){
            $ascil = ord($str[$i]) + $n;

            $pass .= chr($ascil);
        }
        echo $str;echo "</br>";
        return $pass;
    }
    /**解密*/
    public function decrypt($pass,$n){
        $lenth = strlen($pass);
        for($i=0;$i<$lenth;$i++){
            $ascil = ord($pass[$i]);
            $str = chr($ascil - $n) ;
        }
        echo $str;echo "</br>";
        //return $str;
    }




    /**非对称加密*/
    public function test_rec(){
        $data = [
            'uid'=>3,
            'name'=>'王五',
            'age'=>25
        ];
        $json_str = json_encode($data,JSON_UNESCAPED_UNICODE);
        //加密
        $private = openssl_pkey_get_private('file://'.storage_path('app/keys/private.pem'));
        openssl_private_encrypt($json_str,$enc_data,$private);
        var_dump($enc_data);

        //解密
        $public_key = openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($enc_data,$dec_data,$public_key);
        echo "</br>";
        echo $dec_data;

    }


    public function ajaxTest(){
        return view('demo');
    }






    public function accessToken(){
        //Cache::pull('access');exit;
        $access = Cache('access');
        //        var_dump($access);exit;
        if(empty($access)){
            $appid = "wx51db63563c238547";
            $appkey = "35bdd2d4a7a832b6d20e4ed43017b66e";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appkey";
            $method = 1;
            $info = $this->send($url,$method);
            $arrInfo = json_decode($info,true);
            $key = "access";
            $access = $arrInfo['access_token'];
            $time =$arrInfo['expires_in'];
            cache([$key=>$access],$time);
        }

        var_dump($access);
    }



    public function send($url,$arr,$method)
    {
        $ch = curl_init();
        if($method==1){  //get请求
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_HEADER,0);

            $info = curl_exec($ch);
            $code = curl_errno($ch);
            //var_dump($code);
            curl_close($ch);
            return $info;
        }else{  //post请求
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
            curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type:text/plain']);

            $info = curl_exec($ch);
            //$code= curl_errno($ch);
            //var_dump($code);exit;
            curl_close($ch);
            return $info;
        }

    }

}
