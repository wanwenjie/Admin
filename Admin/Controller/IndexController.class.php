<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {

    //前台显示电影首页
    public function index(){
    	layout(false); 
        $this->display('Index:Default/vod_index');
    }



}