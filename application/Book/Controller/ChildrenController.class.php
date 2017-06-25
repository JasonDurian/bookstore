<?php
namespace Book\Controller;
use Common\Controller\HomebaseController;

class ChildrenController extends HomebaseController{

	public function index(){
		$goods_model = M('Goods');    //从数据库中选取数据
		$types_model = M('GoodsType');
		
		$books = $goods_model->where(array('type_id'=>array(array('egt','6'),array('elt','15'))))->select();
		$types = $types_model->where(array('parent'=>'5'))->select();       //parent=5代表的是少儿导航栏
		$this->assign('books',$books);
		$this->assign('types',$types);
		$this->display();
	}
}