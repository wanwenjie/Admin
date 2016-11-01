<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->login();
    }

    //登录界面
    public function login(){
    	$this->display('login');
    }

    //管理界面-主界面
    public function admin_index(){
    	$model = new \Think\Model();
    	$version = $model->query("select version() as ver");
    	$this->assign('mysql_version',$version[0]['ver']);
    	$this->display('admin_index');
    }

    //管理界面-系统设置
    public function admin_setting(){

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
    	$this->display('admin_setting');
    }

    //管理界面-分类设置
    public function admin_category(){
    	$type = M('VodType');
    	$info = $type->order('t_sort asc,t_pid asc')->select();
    	$this->assign('info',$info);
    	$this->display('admin_category');
    }

    //管理界面-添加电影
    public function admin_add(){
    	$this->display('admin_add');
    }

    //管理界面-视频列表
    public function admin_list(){
    	$vod = M('Vod');
        $vod_type = M('VodType');
    	$totalPages = $vod->count();
    	$page = new \Think\Page($totalPages,20);
    	$vodlist = $vod->order('d_maketime desc')->limit($page->firstRow.','.$page->listRows)->select();
    	$show = $page->show();
        //在此操作vodlist 转换d_type 数字为字符串
        foreach ($vodlist as $key => $value) {
            $str = 't_id='.$value['d_type'];
            $res = $vod_type->field('t_name')->where($str)->select()[0]['t_name'];
            $vodlist[$key]['type'] = $res;
        }
    	$this->assign('page',$show);
    	$this->assign('vodlist',$vodlist);
    	$this->display('admin_list');
    }

}