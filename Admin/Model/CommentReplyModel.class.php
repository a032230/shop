<?php  
namespace Admin\Model;
use Think\Model;

class CommentReplyModel extends Model{
	//定义评论时允许接收的字段
 	protected $insertFileds = "content,star,goods_id";

 	//定义表单验证规则
 	protected $_validate = array(
 		array('comment_id','require','参数错误',1),
 		array('content','1,200','内容必须是1-200个字符',1,'length'),
 	);


 	//添加前执行
 	protected function _before_insert(&$data,$option)
 	{
 		//判断是否登陆
 		$memberId = session('m_id');
 		if(!$memberId)
 		{
 			$this -> error = '请先登陆';
 			return false;
 		}

 		//追加信息
 		$data['addtime'] = date('Y-m-d H:i:s');
 		$data['member_id'] = $memberId;
 	}

}