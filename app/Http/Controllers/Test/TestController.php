<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class TestController extends Controller
{
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

}
