<?php

namespace Admin\Model;
use Think\Model;


/**
* 视频分类信息
*/
class VodTypeModel extends Model{
	
	protected $trueTableName = 'mac_vod_type';

	//定义主键
	protected $pk     		 = 't_id';
	//自动完成
	// protected $_auto = array(
	// 	array('t_tpl_list','vod_list.html'),
	// 	);
}