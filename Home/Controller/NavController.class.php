<?php  
namespace Home\Controller;
use Think\Controller;
/*---------------------------
|         导航控制器：       |
| 所有用到导航的都继承该控制器|
---------------------------*/
class NavController extends Controller
{
	public function __construct()
	{
		parent::__construct();

		 $cateModel = D('Admin/category');

        //返回分类前三级分类的六维数组
        $cateData = $cateModel -> getNavData();

        $this -> assign('cateData',$cateData);
	}
}