<?php
namespace Admin\Model;
use Think\Model;
/*------------------------
 |       会员等级模型     |
 ------------------------*/
class MemberLevelModel extends Model 
{
	protected $insertFields = array('level_name','jifen_bottom','jifen_top');
	protected $updateFields = array('id','level_name','jifen_bottom','jifen_top');
	protected $_validate = array(
		array('level_name', 'require', '级别名称不能为空！', 1, 'regex', 3),
		array('level_name', '1,20', '级别名称的值最长不能超过 20 个字符！', 1, 'length', 3),
		array('jifen_bottom', 'require', '积分下限不能为空！', 1, 'regex', 3),
		array('jifen_bottom', 'number', '积分下限必须是一个整数！', 1, 'regex', 3),
		array('jifen_top', 'require', '积分上限不能为空！', 1, 'regex', 3),
		array('jifen_top', 'number', '积分上限必须是一个整数！', 1, 'regex', 3),
	);

	// 删除前
	protected function _before_delete($option)
	{	
		//防止SQL注入
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
	}
}