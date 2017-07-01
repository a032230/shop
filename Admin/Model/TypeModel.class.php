<?php
namespace Admin\Model;
use Think\Model;
class TypeModel extends Model 
{
	protected $insertFields = array('type_name');
	protected $updateFields = array('id','type_name');
	protected $_validate = array(
		array('type_name', 'require', '类型名称不能为空！', 1, 'regex', 3),
		array('type_name', '1,100', '类型名称的值最长不能超过 100 个字符！', 1, 'length', 3),
		array('type_name', '', '类型名称已经存在！', 1, 'unique', 3),
	);
	public function search($pageSize = 20)
	{

		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this->alias('a')->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
		return $data;
	}
	// 删除前
	protected function _before_delete($option)
	{

		//删除类型时把旗下的属性也删除掉
		
		$attrModel = M('attr');
		$attrModel -> where("type_id=$id") -> delete();
	}

}