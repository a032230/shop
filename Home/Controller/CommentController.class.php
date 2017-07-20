<?php  
namespace Home\Controller;
use  Think\Controller;

class CommentController extends Controller
{
	// ajax评论商品
	public function add()
	{
		if(IS_POST){
			$model = D('Admin/comment');
			//接收验证表单并保存到模型
			if($model -> create(I('post.'),1)){
				//执行添加
				if($id = $model -> add()){
					//添加成功后直接把添加的数据返回显示
					$this -> success(array(
							'id' => $id,
							'face' => session('face'),
							'username' => session('m_user'),
							'addtime' => date('Y-m-d H:i:s'),
							'content' => I('post.content'),
							'star' => I('post.star'),
						),'',true);
				}
			}
			//执行失败
			//第三个参数为true返回json数据
			$this -> error($model -> getError(),'',TRUE);
		}
	}

	//ajax获取数据
	public function ajaxPl()
	{
		$goodsId = I('get.id');
		$model = D('Admin/comment');
		$data = $model -> search($goodsId,5);
		// p($data);die;
		//返回json数据
		echo json_encode($data);
	}


	// ajax回复
	public function ajaxReply()
	{
		if(IS_POST){
			$model = D('Admin/CommentReply');
			//接收验证表单并保存到模型
			if($model -> create(I('post.'),1)){
				//执行添加
				if( $model -> add()){
					//添加成功后直接把添加的数据返回显示
					$this -> success(array(
							'face' => session('face'),
							'username' => session('m_user'),
							'addtime' => date('Y-m-d H:i:s'),
							'content' => I('post.content'),
						));
				}
			}
			//执行失败
			//第三个参数为true返回json数据
			$this -> error($model -> getError());
		}
	}
}