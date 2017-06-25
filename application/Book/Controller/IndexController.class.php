<?php
namespace Book\Controller;
use Common\Controller\HomebaseController;

class IndexController extends HomebaseController{
	
	protected $goods_model;
	
	public function _initialize() {
		parent::_initialize();
		$this->goods_model = M('Goods');
	}
	
	public function index() {
// 		$goods_model = M('Goods');    //从数据库中选取数据
		$types_model = M('GoodsType');
		$slide_model = M('AppAd');

		//根据parent来选取type_id (暂时未实现 ) 还要注意每个子分类的书籍个数
// 		$types_id = $types_model->field('type_id')->where(array('parent'=>'1'))->select();
// 		foreach ($types_id as $value) {
// 			;
// 		}

		$books = $this->goods_model->field('goods_id,type_id,name,pre_price,now_price,cover')->where(array('type_id'=>array('in','2,3,4')))->select();
		$types = $types_model->where(array('parent'=>'1','status'=>array('neq','3')))->select();      //parent=1代表的是推荐导航栏
		
		$slides = $slide_model->field('image,skip_type,aim_id,url')->where(array('slide_status'=>array('eq',1)))->select();
		foreach($slides as $i=>$vo) {
			if($vo['skip_type'] == 1) {             //detail
				$slides[$i]['url'] = leuu('Detail/index',array('id'=>$vo['aim_id']));
			}
		}
		$sliCount = $slide_model->where(array('slide_status'=>array('eq',1)))->count();
// 		$slides_start = $slide_model->order('id')->find();
// 		$slides_end = $slide_model->order('id desc')->find();
		
		$this->assign('books',$books);
		$this->assign('types',$types);
		$this->assign('slides',$slides);
		$this->assign('sliCount',$sliCount-1);
// 		$this->assign('slides_start',$slides_start);
// 		$this->assign('slides_end',$slides_end);
		$this->display();
	}
	
	public function search() {
		$keyword = I("post.keyword");
		
		if (!empty($keyword)) {
			
			$keywords = explode(' ', $keyword);
			foreach($keywords as $i=>$vo) {
				$keywords[$i] = '%'.$vo.'%';
			}
			$where['CONCAT(name,params,detail)'] = array('like',$keywords,'AND');		//多字段模糊查询
				
			$books = $this->goods_model->where($where)->page(1,10)->select();
			if(empty($books)) {
				$remind = '暂无相关结果.';
			}
			$this -> assign("keyword", $keyword);
			$this -> assign("books", $books);
		} else {
			$remind = '输入为空.';
		}
		$this->assign("remind", $remind);
		$this -> display();
	}
	
	public function getLimit() {
		$keyword = I('post.keyword');
		$type_id = I('post.tid');
		$page_num = I('post.page');
		
		//搜索中的滚动加载
		if(!empty($keyword)) {
			$keywords = explode(' ', $keyword);
			foreach($keywords as $i=>$vo) {
				$keywords[$i] = '%'.$vo.'%';
			}
			$where['CONCAT(name,params,detail)'] = array('like',$keywords,'AND');		//多字段模糊查询
			
			$books = $this->goods_model->where($where)->page($page_num,10)->select();
			$res['result']=$books;
			$this->ajaxReturn($res, 'json');
		}
		
		//分类中的滚动
		if(!empty($type_id)) {
			$books = $this->goods_model->where(array('type_id'=>$type_id))->page($page_num,10)->select();
			$res['result']=$books;
			$this->ajaxReturn($res, 'json');
		}
	}
	
	public function test() {
		//redis
		$options = array(
			'type'  	=>'Redis',
			'prefix'	=>'redis_',
			'expire'	=>300,
			'timeout'	=>300,
			'persistent'=>false,
				
// 			'length'	=> 3,
// 			'queue'		=> 'redis',
// 			'queue_name'=> 'book_queue',
		);
// 		$cache = S($options);
		
// 		$cache->rPush('key1', 'A');
// 		$cache->rPush('key1', 'B');
// 		$cache->rPush('key1', 'C');
// 		dump($cache->lRange('key1', 0, -1)); /* array('A', 'B', 'C') */
// 		$cache->lTrim('key1', 0, 1);
// 		dump($cache->lRange('key1', 0, -1)); /* array('A', 'B') */
		
// 		$cache->hSet('test', 'test1', '123');
// 		$cache->hSet('test', 'test2', '123');
// 		$cache->hSet('test', 'test3', '222');
	
// 		S('test','222',$options); //set
// 		$rst = S('test');		  //get
// 		S('test',null,$options);  //remove

		dump();
	}
	
}