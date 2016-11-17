<?php

namespace Admin\TagLib;
use Think\Template\TagLib;

/**
* 自定义标签库
* 仿照maccms 苹果cms
* 
*/
class Maccms extends TagLib{
	
	protected $tags = array(
		'include'	=> array('close'=>0),
		'head'		=> array('close'=>0),
		'foot' 		=> array('close'=>0),
		'path_tpl'	=> array('close'=>0),
		'email'		=> array('close'=>0),
		'name'		=> array('close'=>0),
		'url'		=> array('close'=>0),
		'vod'		=> array('attr'=>'num,order,by,type,level','close'=>1),



		);

	public function _path_tpl(){
		$str = APP_PATH.'Admin/View/Index/'.C('M_Theme').'/';
		return $str;
	}
	//包含include文件
	public function _include(){
		$path = $this->_path_tpl()."home_include.html";
		$str = file_get_contents($path);
		return $str;
	}

	public function _name(){
		return $str = C('M_Name');
	}

	public function _url(){
		return $str = C('M_Domain');
	}

	public function _email(){
		return $str = C('M_Email');
	}

	//包含头部文件
	public function _head($tag,$content){
		$path = $this->_path_tpl()."home_head.html";
		$str = file_get_contents($path);
		return $str;
	}

	//包含底部文件
	public function _foot($tag,$content){
		$path = $this->_path_tpl()."home_foot.html";
		$str = file_get_contents($path);
		return $str;
	}

	//解析vod标签
	public function _vod($tag,$content){
		$num = $tag['num'];
		$order = $tag['order'];
		$by = $tag['by'];
		$type = $tag['type'];
		$level = $tag['level'];
		$pagesize = $tag['pagesize'];
		if(!empty($type)){
			$map['d_type'] = $type;
		}
		if(!empty($level)){
			$map['d_level'] = $level;
		}
		if(!empty($by) && !empty($order)){
			$o = 'd_maketime '.$order;
		}
		// if(!empty($pagesize)){
		// 	$p = $pagesize;
		// }
		if(!empty($num)){
			$n = $num;
		}
		//取出合适的数据
		$vod = M('Vod');
		$data = $vod->where($map)->order($o)->limit('0,'.$n)->select();
		foreach ($data as $key => $value) {
		$search = array("[vod:playlink]","[vod:name]","[vod:pic]","[vod:remarks]");
		$replace = array($value["d_playlink"],$value["d_name"],$value["d_pic"],$value["d_remarks"]);
		$c = str_replace($search,$replace,$content);
		$str .= $c;
		}
		return $str;
		
		


	}



}