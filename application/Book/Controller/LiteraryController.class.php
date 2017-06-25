<?php
namespace Book\Controller;
use Common\Controller\HomebaseController;

class LiteraryController extends HomebaseController{
	
	public function index(){
		$goods_model = M('Goods');    //从数据库中选取数据
		$types_model = M('GoodsType');
		
		$books = $goods_model->where(array('type_id'=>array(array('egt','17'),array('elt','17'))))->select();
		$types = $types_model->where(array('parent'=>'16'))->select();            //parent=16代表的是文创导航栏
		$this->assign('books',$books);   
		$this->assign('types',$types);
		$this->display();
	}
}