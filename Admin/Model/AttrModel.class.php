<?php
namespace Admin\Model;
use Think\Model;
/*--------------------------
|         属性模型          |
--------------------------*/
class AttrModel extends Model 
{	
	//添加时允许接收的字段
	protected $insertFields = array('attr_name','attr_type','attr_option_values','type_id');
	//修改时允许接收的字段
	protected $updateFields = array('id','attr_name','attr_type','attr_option_values','type_id');

	//验证规则
	protected $_validate = array(
		array('attr_name', 'require', '属性名不能为空！', 1, 'regex', 3),
		array('attr_name', '1,20', '属性名的值最长不能超过 20 个字符！', 1, 'length', 3),
		array('attr_type', 'require', '属性类型不能为空！', 1, 'regex', 3),
		array('attr_type', '唯一,可选', "属性类型的值只能是在 '唯一,可选' 中的一个值！", 1, 'in', 3),
		array('type_id', 'require', '所属类型id不能为空！', 1, 'regex', 3),
		array('type_id', 'number', '所属类型id必须是一个整数！', 1, 'regex', 3),
	);
	public function search($pageSize = 20)
	{
		/**************************************** 搜索 ****************************************/
		$where = array();
		if($attr_name = I('get.attr_name'))
			$where['attr_name'] = array('like', "%$attr_name%");
		$attr_type = I('get.attr_type');
		if($attr_type != '' && $attr_type != '-1')
			$where['attr_type'] = array('eq', $attr_type);
		if($attr_option_values = I('get.attr_option_values'))
			$where['attr_option_values'] = array('like', "%$attr_option_values%");
		if($type_id = I('get.type_id'))
			$where['type_id'] = array('eq', $type_id);
		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this->field('a.*,b.type_name')
		                     ->alias('a')
		                     ->join('LEFT JOIN __TYPE__ b ON a.type_id=b.id')
		                     ->where($where)
		                     ->group('a.id')
		                     ->limit($page->firstRow.','.$page->listRows)
		                     ->select();
		return $data;
	}
	// 添加前
	protected function _before_insert(&$data, $option)
	{
		//将中文逗号换成英文
		$data['attr_option_values'] = str_replace('，',',' , $_POST['attr_option_values']);
	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
		//将中文逗号换成英文
		$data['attr_option_values'] = str_replace('，',',' , $_POST['attr_option_values']);
	}
	// 删除前
	protected function _before_delete($option)
	{
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
	}
}