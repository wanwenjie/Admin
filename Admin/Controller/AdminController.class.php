<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller {

	//后台控制器
	public function index(){
        $this->login();
    }

    /**
     * 登录界面
     * *************************************************************************************
     */
    public function login(){
    	$this->display('login');
    }


    /**
     * 管理界面-主界面
     * *************************************************************************************
     */
    public function admin_index(){
    	$model = new \Think\Model();
    	$version = $model->query("select version() as ver");
    	$this->assign('mysql_version',$version[0]['ver']);
    	$this->display('admin_index');
    }

    /**
     * 管理界面-系统设置界面
     * *************************************************************************************
     */
    public function admin_setting(){
        //自动检测View/Index 目录下有多少目录
        $theme_dir = APP_PATH.'Admin/View/Index';
        $dir = array();
        if(is_dir($theme_dir)){
            $dh = opendir($theme_dir);
            while(($file = readdir($dh))!== false){
                if($file != '.' && $file != '..'){
                    $dir[] = $file;  
                }
            }
        }
    	if(IS_POST){
    		$post = I('post.');
    		$path = APP_PATH."Admin/Conf/verify.php";
    		$arr = include $path;
    		$merge = array_merge($arr,$post);
    		$str = "<?php \n return array(\n";
    		foreach ($merge as $key => $value) {
    			$str .= "'".$key."'\t=>\t'".$value."',\n";
    		}
    		$str .= ");\n";
			file_put_contents($path,$str); 
    	}
		$info = array(
    		'M_Name'				=> C('M_Name'),
		    'M_Domain'				=> C('M_Domain'),
		    'M_Keywords'			=> C('M_Keywords'),
		    'M_Content'				=> C('M_Content'),
		    'M_Email'				=> C('M_Email'),
		    'M_Theme'				=> C('M_Theme')
		);
    	$this->assign('info',$info);
        $this->assign('dir',$dir);
    	$this->display('admin_setting');
    }

    /**
     * 管理界面-分类设置
     * *************************************************************************************
     */
    public function admin_category(){
        $type = D('VodType');
        if(IS_POST){
            //过滤之后添加
            //$post = I('post.');
            //print_r($post);
            $type_1 = M('VodType');
            $type_1->create();
            $type_1->add();
            //$type->add($post);
        }
        if(IS_GET){
            $id = I('get.t_id');
            $type->delete($id);
        }
    	$arr = $this->getCateArr();
    	$this->assign('info',$arr);
    	$this->display('admin_category');
    } 

    //添加分类
    public function admin_category_add(){
        $id = I('get.t_id');
        $type = D('VodType');
        if(IS_POST){
            $post = I('post.');
            $type->save($post);
        }
        $data = $type->where('t_id='.$id)->select();
        $arr = $this->getCateArr();
        $this->assign('arr',$arr);
        $this->assign('info',$data[0]);
        $this->display('admin_category_add');
    }

    //获取分类数组
    public function getCateArr(){
        //实例化
        $type = M('VodType');
        $info = $type->order('t_sort asc,t_pid asc')->select();
        $arr = $this->subTree($info,0, 0);
        return $arr;
    }

    //无限极分类之递归寻找子栏目
    public function subTree($arr,$id,$lev){
        //寻找$arr数组中$id下的所有子栏目
        static $subs = array();
        foreach ($arr as $key => $value) {
            if($value['t_pid'] == $id){
                $value['t_lev'] = str_repeat('|--', $lev);
                $subs[] = $value;
                $this->subTree($arr,$value['t_id'],$lev+1);
            }
        }
        return $subs;

    }

    /**
     * 管理界面-添加电影
     * *************************************************************************************
     */
    public function admin_add(){
        $this->assign('error',$error);
        //赋值分类列表
        $arr = $this->getCateArr();
        $this->assign('arr',$arr);
        //获取推荐列表
        $this->assign('level',C('M_level'));
        //获取地区列表
        $this->assign('area',C('M_area'));
        //获取语言列表
        $this->assign('lang',C('M_lang'));
        //获取播放器
        $this->assign('player',C('M_player'));
    	$this->display('admin_add');
    }

    //设置火车头接口
    public function admin_interface(){
        foreach (I('post.') as $key => $value) {
            
            if($key == "d_playfrom" || is_array($value)){
                $data[$key] = implode('$$$',$value);
            }
            else if($key == "d_playurl" || is_array($value)){
                $data[$key] = implode('$$$',$value);
            }else $data[$key] = $value;
        }
        if(IS_POST){
            $vod = D('Vod');
            if(!$vod->create($data)){
                $this->ajaxReturn($vod->getError());
            }else $vod->add();
        }
    }

    //图片上传
    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     APP_PATH.'/Admin/Uploads/'; // 设置附件上传根目录
        $upload->saveName  =     date('Y-m-d',time()).'_'.mt_rand();
        // 上传单个文件 
        $info   =   $upload->uploadOne($_FILES['pic']);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            
            echo $info['savepath'].$info['savename'];
        }
    }



    /**
     * 管理界面-视频列表
     * *************************************************************************************
     */
    public function admin_list(){

        $vod = M('Vod');
        
        //删除一个或者多个电影
        if(IS_POST){
            $id = I('post.d_id');
            $vod->delete($id);
        }

        $vod_type = M('VodType');

        //初始化总数
        $totalPages = $vod->count();

        //实例化分页类对象
        $page = new \Think\Page($totalPages,20);
        
        //获取数据
        $vodlist = $vod->order('d_maketime desc')->limit($page->firstRow.','.$page->listRows)->select();
        //显示
        $show = $page->show();

        //在此操作vodlist 转换d_type 数字为字符串
        foreach ($vodlist as $key => $value) {
            $str = 't_id='.$value['d_type'];
            $res = $vod_type->field('t_name')->where($str)->select();
            $name = $res[0]['t_name'];
            $vodlist[$key]['type'] = $name;
        }
        //获取分类列表
        $arr = $this->getCateArr();
        $this->assign('arr',$arr);
        //获取推荐列表
        $this->assign('level',C('M_level'));
        //获取地区列表
        $this->assign('area',C('M_area'));
        //获取语言列表
        $this->assign('lang',C('M_lang'));
        //分页显示
    	$this->assign('page',$show);
    	$this->assign('vodlist',$vodlist);
    	$this->display('admin_list');
    }
    
    public function admin_list_update(){
        $vod = M('Vod');
        foreach (I('post.') as $key => $value) {
            if($key != 'd_id'){
                $data[$key] = $value;
            }
        }
        $ids = I('post.d_id');
        if(stripos($ids,',')){
            $ids = substr($ids,0,-1);
            $id = explode(',',$ids);
            foreach ($id as $key => $value) {
                $vod->where('d_id='.$value)->save($data);
            }
        }else{
            $vod->where('d_id='.$ids)->save($data);
        }
    }

    //管理界面-修改电影
    
    public function admin_list_modify(){
        if(IS_POST){
            $vod = M('Vod');
            $vod->create();
            $vod->save();
        }
        $id = I('get.d_id');
        $vod = D('Vod');
        $data = $vod->where('d_id='.$id)->select();
        //获取分类列表
        $arr = $this->getCateArr();
        $this->assign('arr',$arr);
        //获取推荐列表
        $this->assign('level',C('M_level'));
        //获取地区列表
        $this->assign('area',C('M_area'));
        //获取语言列表
        $this->assign('lang',C('M_lang'));
        //获取播放器
        $this->assign('player',C('M_player'));

        //如果有多个来源
        $playfrom = explode('$$$',$data[0]['d_playfrom']);
        $playurl = explode('$$$',$data[0]['d_playurl']);

        foreach ($playfrom as $key => $value) {
            $play[] = array('from' =>$value,'url'=>$playurl[$key]);
        }
        $data[0]['d_play'] = $play;
        $this->assign('data',$data[0]);
        $this->display('admin_list_modify');
    }

    //视频列表之搜索功能
    public function search(){
        $vod = M('Vod');
        $vod_type = M('VodType');
        //搜索开始
        if(IS_POST){
            $data = I('post.');
            foreach ($data as $key => $value) {
                if(!empty($value)){
                    if($key == 'q'){ 
                        $c['d_name'] = array('like',"%".$value."%"); 
                        $arr[$key] = $value;
                    }else{
                        $c[$key] = $value;
                    }
                }else{
                    $arr[$key] = $value;
                }
            }
            //将post过来的数组添加到cookie中
            session('search',$c);  //设置session
        }

        $map = session('search');
        $total = $vod->where($map)->count();
        $pageS = new \Think\Page($total,20);
        $vodlist = $vod->where($map)->order('d_maketime desc')->limit($pageS->firstRow.','.$pageS->listRows)->select();
        $show = $pageS->show();
        //在此操作vodlist 转换d_type 数字为字符串
        foreach ($vodlist as $key => $value) {
            $str = 't_id='.$value['d_type'];
            $res = $vod_type->field('t_name')->where($str)->select();
            $name = $res[0]['t_name'];
            $vodlist[$key]['type'] = $name;
        }

        $map = array_merge($map,$arr);
        //将cookie存下的数据返回给下拉框
        $this->assign('search',$map);
        //获取分类列表
        $arr = $this->getCateArr();
        $this->assign('arr',$arr);
        //获取推荐列表
        $this->assign('level',C('M_level'));
        //获取地区列表
        $this->assign('area',C('M_area'));
        //获取语言列表
        $this->assign('lang',C('M_lang'));
        //分页显示
        $this->assign('page',$show);
        $this->assign('vodlist',$vodlist);
        $this->display('admin_list');
        
    }

    public function admin_make(){
    	//获取分类列表
        $arr = $this->getCateArr();
        $this->assign('arr',$arr);
    	$this->display('admin_make');
    }


}