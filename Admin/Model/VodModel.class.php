<?php

namespace Admin\Model;
use Think\Model;


/**
* 视频信息
*/
class VodModel extends Model{
	
	protected $trueTableName = 'mac_vod';

	protected $patchValidate = true;//返回验证错误信息为数组

	//自动验证
	protected $_validate = array(
		array('d_type','require','分类必须!'),
		array('d_name','require','名称必须!'),

		);


	protected $_auto = array(
		array('d_subname','',1),
		array('d_enname','',1),
		array('d_color','',1),
		array('d_letter','',1),
		//array('d_pic','',1),
		array('d_picthumb','',1),
		array('d_picslide','',1),
		array('d_starring','',1),
		array('d_directed','',1),
		array('d_tag','',1),
		array('d_remarks','',1),
		//array('d_area','',1),
		//array('d_lang','',1),
		array('d_year','',1),
		//array('d_type','',1),
		array('d_type_expand','',1),
		array('d_class','',1),
		// array('d_topic','',1),
		// array('d_hide','',1),
		array('d_lock','',1),
		// array('d_state','',1),
		// array('d_level','',1),
		// array('d_usergroup','',1),
		// array('d_stint','',1),
		// array('d_stintdown','',1),
		// array('d_dayhits','',1),
		// array('d_weekhits','',1),
		// array('d_monthhits','',1),
		// array('d_duration','',1),
		// array('d_up','',1),
		// array('d_down','',1),
		// array('d_score','',1),
		array('d_scoreall','',1),
		//array('d_scorenum','',1),
		array('d_addtime','time',3,'function'),
		array('d_time','time',3,'function'),
		array('d_hitstime','',1),
		array('d_maketime','time',1,'function'),
		array('d_content','',1),
		//对这两个数据进行操作
		//array('d_playfrom','merge',1,'callback'),
		//array('d_playurl','merge',1,'callback'),

		array('d_playserver','',1),
		array('d_playnote','',1),
		array('d_downfrom','',1),
		array('d_downserver','',1),
		array('d_downnote','',1),
		array('d_downurl','',1),
		 );

}