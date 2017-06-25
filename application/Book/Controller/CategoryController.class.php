<?php
namespace Book\Controller;
use Common\Controller\HomebaseController;

class CategoryController extends HomebaseController{
	
	public function index(){
		$types_model = M('GoodsType');
		$types = $types_model->where(array('parent'=>'18'))->select();    //取出分类导航当中的子类
		
		$this->assign('types',$types);
		$this->display();
	}
	
	//显示四个导航当中子类的模板
	public function category() {
		$type_id=I('get.tid',0,'intval');
		$goods_model = M('Goods');
		$types_model = M('GoodsType');
		
		$books = $goods_model->where(array('type_id'=>$type_id))->page(1,10)->select();
		$types = $types_model->field('name,type_id')->where(array('type_id'=>$type_id))->find();    
		
		$this->assign('books',$books);
		$this->assign('types',$types);
		$this->display();
	}
}
