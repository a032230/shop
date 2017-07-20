<?php  
namespace Admin\Model;
use Think\Model;
/*------------------------
 |       评论模型         |
 ------------------------*/
 class CommentModel extends Model{

 	//定义评论时允许接收的字段
 	protected $insertFileds = "content,star,goods_id";

 	//定义表单验证规则
 	protected $_validate = array(
 		array('goods_id','require','参数错误',1),
 		array('star','1,2,3,4,5','分值只能是1-5之间的数字',1,'in'),
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

 		//将买家印象写入印象表
 		$yxModel = M('yinxiang');
 		$_yx = I('post.yx_name');
 		$yxId = I('post.yx_id');
 		//处理旧印象
 		if($yxId)
 		{
 			$yxModel -> where(array('id' => array('IN',$yxId))) -> setInc('yx_count');
 		}

 		//新的印象
 		if($_yx)
 		{
 			$yx = str_replace('，', ',', $_yx);
 			$yx = explode(',', $yx);
 			foreach($yx as $k => $v)
 			{
 				$v = trim($v);
 				if(empty($v)){
 					continue;
 				}
 				//判断这个印象是否存在
 				$has = $yxModel -> where(array(
 						'goods_id' => $data['goods_id'],
 						'yx_name' => $v,
 					))->find();
 				if($has){
 					$yxModel ->where(array(
 							'goods_id' => $data['goods_id'],
 							'yx_name' => $v,
 						)) -> setInc('yx_count');
 				}else{
 					$yxModel -> add(array(
 							'goods_id' => $data['goods_id'],
 							'yx_name' => $v,
 							'yx_count' => 1,
 						));
 				}
 			}
 		}
 	}

 	//取数据并制作ajax翻页
 	public function search($goodsId,$pagesize)
 	{
 		$where['a.goods_id'] = array('eq',$goodsId);
 		/*************计算分页*********/
 		//取出总记录数
 		$count = $this -> alias('a') -> where($where) -> count();

 		//计算总页数
 		$pageCount = ceil($count / $pagesize);
 		//获取当前页
 		$currentPage = max(1,(int)I('get.p',1));
 		//计算limit的第一个参数:偏移量
 		$offset = ($currentPage -1) * $pagesize;

 		//在获取第一页数据的时候取得该商品的好评率
 		if($currentPage == 1){
	 		$stars = $this -> alias('a') -> where($where) -> select();
	 		$hao = $zhong = $cha = 0;
	 		foreach($stars as $k => $v)
	 		{
	 			if($v['star']  == 3)
	 				$zhong++;
	 			elseif($v['star'] > 3)
	 				$hao++;
	 			else
	 				$cha++;
	 		}
	 		$total = $hao + $zhong + $cha; //总的评论数
	 		$hao = round(($hao / $total) * 100,2);
	 		$zhong = round(($zhong / $total) * 100,2);
	 		$cha = round(($cha / $total) * 100,2);

	 		//获取印象数据
	 		$yxModel = M('yinxiang');
	 		$yxData = $yxModel -> where("goods_id = $goodsId") -> select();
 		}
 		/***************取数据************/
 		$data = $this -> alias('a') 
 					  -> field("a.id,a.addtime,a.content,a.star,a.click_count,b.username,b.face,COUNT(c.id) reply_count")
 					  -> join("LEFT JOIN __MEMBER__ b ON a.member_id=b.id
 					  			LEFT JOIN __COMMENT_REPLY__ c ON a.id = c.comment_id")
 					  -> where($where)
 					  -> order('a.id DESC')
 					  -> limit("$offset,$pagesize")
 					  -> group('a.id')
 					  -> select();
 		// echo $this -> getLastSql();
 		
 		//取回复
 		$crModel = M('comment_reply');
 		foreach($data as $k => &$v)
 		{
 			$v['reply'] = $crModel -> alias('a')
 			 					   -> field("a.content,a.addtime,b.username,b.face")
 			 					   -> join("LEFT JOIN __MEMBER__ b ON a.member_id = b.id")
 			 					   -> where(array('a.comment_id' => $v['id']))
 			 					   -> order('a.id ASC')
 			 					   -> select();
 		}
 		// echo $crModel -> getLastSql();
 		//返回数据
 		return array(
 			'data' => $data,
 			'page' => $pageCount,
 			'hao' => $hao,
 			'zhong' => $zhong,
 			'cha' => $cha,
 			'yxData' => $yxData,
 		);
 	}

 }