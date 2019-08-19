<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    /**聊天室*/
    public function chat(){
        return view('index.chat');
    }
    public function chatdo(){

    }
}
