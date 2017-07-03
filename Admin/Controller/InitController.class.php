<?php  
namespace Admin\Controller;
use Think\Controller;
/*-------------------------------------*
 *初始化控制器                          *
 *除登陆控制器外所有控制器都继承此控制器  *
 *------------------------------------*/
 class InitController extends Controller{
 	
 	public function __construct()
 	{
 		//继承父类构造方法
 		parent::__construct();

 		//判断登陆
 		session('id') ? session('id') : $this -> error('请先登陆',U('Login/login'));


 		/**************判断权限*****************/

 		//所有管理员都可以进入后台首页
 		if(CONTROLLER_NAME == 'Index'){
 			return true;
 		}

 		$authModel = D('auth');
 		if(!$authModel -> checkAuth())
 		{
 			$this -> error('无权访问！');
 		}
 	}



 }