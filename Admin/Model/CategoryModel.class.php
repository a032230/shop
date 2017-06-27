<?php
namespace Admin\Model;
use Think\Model;

/*------------------------
 |       分类模型         |
 ------------------------*/
class CategoryModel extends Model{

	//定义添加时允许接收的字段
	protected $insertFields = 'parent_id,cat_name';

	//定义修改时允许接收的字段
	protected $updateFields = 'id,parent_id,cat_name';

	//定义验证规则
	protected $_validate = array(
		array('cat_name', 'require','分类不能为空',1),
	);



	/**
	 * [getChildren  获取该分类id下的所有子类id
	 * @param  [type] $catid [传递进来的分类id]
	 * @return [array]        [返回所有该分类下的所有子类id]
	 */
	public function getChildren($catid)
	{
		$data = $this -> select();

		return $this -> _getChildren($data,$catid,TRUE);
	}

	/**
	 * [_getChildren 递归查找所有子类
	 * @param  [array]  $data     [分类数据]
	 * @param  [int]  $catid    [分类id]
	 * @param  boolean $is_clean [是否清空$ret]
	 * @return [$ret]  array     [该分类id下的所有子类id]
	 */
	private function _getChildren($data,$catid,$is_clean = FALSE)
	{
		static $ret = array(); //定义静态数组用于存放子类id

		//首次调用，先清空数组，防止多次调用该方法时保存上次继承的数据
		if($is_clean)
		{
			$ret = array();
		}

		//遍历分类数据获取所有子类id
		foreach($data as $v){
			if($v['parent_id'] == $catid){
				$ret[] = $v['id'];

				//再查找该$v['id']下的子类
				$this -> _getChildren($data,$v['id']);
			}
		}
		return $ret;
	}

	/**
	 * [getTree 打印树状结构数据
	 * @return [array] [标记好分类等级的数据]
	 */
	public  function getTree()
	{
		$data = $this -> select();
		return $this -> _getTree($data);
	}


	/**
	 * [_getTree 为所有分类标记等级
	 * @param  [type]  $data      [分类数据]
	 * @param  integer $parent_id [父级id]
	 * @param  integer $level     [分类等级]
	 * @return [array] $ret       [所有标记好等级的分类数据]
	 */
	private function _getTree($data,$parent_id = 0 ,$level = 0)
	{
		static $ret = array();

		foreach($data as $v){
			//查找出所有的顶级id
			if($v['parent_id'] == $parent_id){
				//标记该分类等级
				$v['level'] = $level;
				$ret[] = $v;

				$this -> _getTree($data,$v['id'],$level + 1);
			}
		}

		return $ret;
	}

	protected function _before_delete(&$option)
	{
		// p($option);die;
		//获取该分类下所有子类id
		$childrens = $this -> getChildren($option['where']['id']);
		$childrens[] = $option['where']['id']; //将分类id压入数组
		//引用传值修改TP底层option,重组$option['where']['id']将分类id和其子类id一起传递回delete方法
		$option['where']['id'] = array(0 => 'IN',1 => implode(',', $childrens));
	}
}