<?php
namespace Book\Controller;
use Common\Controller\HomebaseController;

class DetailController extends HomebaseController{
	
	public function index(){
		$this->baseFun();
		$this->display();
	}
	
	public function comment() {
		$goods_id=I('get.id',0,'intval');
		$comments_model = M('GoodsComments');
		$users_model = M('Member');
		$comments = $comments_model->where(array('goods_id'=>$goods_id))->order(array('create_time'=>'DESC'))->select();

		foreach($comments as $i=>$vo) {
			if($vo['type'] == 0) {
				$comments[$i]['nick_name'] = rand(1,9).'*****'.chr(rand(97, 122));				//匿名
				$comments[$i]['avatar'] = 'avatar/default.png';
			}else {
				$members = $users_model->field('nick_name,avatar')->where(array('member_id'=>$vo['member_id']))->find();
				$comments[$i]['nick_name'] = $members['nick_name'];
				$comments[$i]['avatar'] = $members['avatar'];
			}
		}
		
		$this->assign('comments',$comments);
		$this->baseFun();
		$this->display();
	}
	
	private function baseFun() {
		$user_id = sp_get_current_userid();
		$goods_id=I('get.id',0,'intval');
			
		$goods = M('Goods');    //从数据库中选取数据
		$favorite_model = M('MemberCollection');
		$comments_model = M('GoodsComments');
		
		$where = array(
				'member_id' => $user_id,
				'goods_id' => $goods_id,
		);
		$favCount = $favorite_model->where($where)->count();
		$commCount = $comments_model->where(array('goods_id'=>$goods_id))->count();
		
		//计算评分
		$scores = $comments_model->field('score')->where(array('goods_id'=>$goods_id))->select();
		$totalScore = 0;
		for ($i = 0; $i < $commCount; $i++) {
			$totalScore += $scores[$i]['score'];
		}
		$score = $totalScore != 0 ? ceil($totalScore / $commCount) : 5;
		
		$book = $goods->where(array('goods_id'=>$goods_id))->find();
		
		$this->assign('myFav',$favCount);
		$this->assign('commCount',$commCount);
		$this->assign('book',$book);
		$this->assign('score', $score);
		$this->assign("params",json_decode($book['params'],true));
	}
	
}
