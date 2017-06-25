<?php
namespace Book\Controller;
use Common\Controller\AdminbaseController;

class OrderadminController extends AdminbaseController{
	
	protected $goods_model;
	protected $order_model;
	protected $orderDetail_model;
	
	function _initialize() {
		parent::_initialize();
		$this->goods_model = D("Book/Goods");
		$this->order_model = D("Book/Order");
		$this->orderDetail_model = D("Book/OrderDetail");
	}
	
	public function index(){
		$term_id=I('request.term',0,'intval');
		$this->_lists();
		$this->assign("taxonomys", $term_id);
		$this->display();
	}
	
	private function _lists($where=array()){
		$term_id=I('request.term',0,'intval');
		if(!empty($term_id)){
			$where['status']=$term_id;
			$term=$this->order_model->where(array('status'=>$term_id))->find();
			$this->assign("term",$term);
		}
	
		$start_time=I('request.start_time');
		if(!empty($start_time)){
			$start_time = strtotime($start_time);
			$where['create_time']=array(
				array('EGT',$start_time)
			);
		}
	
		$end_time=I('request.end_time');
		if(!empty($end_time)){
			$end_time = strtotime($end_time);
			if(empty($where['create_time'])){
				$where['create_time']=array();
			}
			array_push($where['create_time'], array('ELT',$end_time));
		}
	
		$keyword=I('request.keyword');
		if(!empty($keyword)){
			$where[] = array(
				'order_id' => array('like',"%$keyword%"),
				'order_sn' => array('like',"%$keyword%"),
				'remark' => array('like',"%$keyword%"),
				'_logic' => 'OR',
			);
		}
		$this->order_model->where($where);
	
		$count=$this->order_model->count();
			
		$page = $this->page($count, 20);
		$this->order_model
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order("order_id DESC");
		
		$orders=$this->order_model->select();
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("orders",$orders);
	}
	
	public function confirmOrder() {
		$order_id=I('request.id',0,'intval');
		$order_sn = $this->order_model->where(array('order_id'=>$order_id))->getField('order_sn');
		
		$this->assign('order_id',$order_id);
		$this->assign('order_sn',$order_sn);
		$this->display();
	}
	
	public function confirmDelivery() {
		$order_id=I('request.order_id',0,'intval');
		$express=I("post.exp");
		$data = array(
			'exp_code' => $express['code'],
			'exp_sn'   => $express['sn'],
			'exp_time' => time(),
			'status'   => 3,
		);
		$rst = $this->order_model->where(array('order_id'=>$order_id))->save($data);
		if ($rst!==false) {
			$result = $this->order_model
						  ->field('order_id,order_sn')
						  ->where(array('order_id'=>array('neq',$order_id),'status'=>2))
						  ->limit(1)->find();				//自动选取下一订单
			if(!empty($result)) {
				$result['href'] = leuu('Book/Shopping/adminDetail',array('orderID'=>$result['order_id']));
				$this->ajaxReturn(sp_ajax_return($result,"保存成功！",1),'json');
			}else {
				$this->success("保存成功,但没有需要发货的订单了");
			}
			
// 			$this->success("保存成功！");
		} else {
			$this->error("保存失败！");
		}
	}
	
		
	public function editOrder() {
		$order_id=I('request.id',0,'intval');
		$delivery = $this->order_model->field('exp_code,exp_sn')->where(array('order_id'=>$order_id))->find();
		$this->assign('order_id',$order_id);
		$this->assign($delivery);
		$this->display();
	}
	
	public function editDelivery() {
		$order_id=I('request.order_id',0,'intval');
		$express=I("post.exp");
		$data = array(
			'exp_code' => $express['code'],
			'exp_sn'   => $express['sn'],
		);
		$rst = $this->order_model->where(array('order_id'=>$order_id))->save($data);
		if ($rst!==false) {
			redirect(leuu("Orderadmin/index"),1,"保存成功");
		} else {
			$this->error("保存失败！");
		}
	}
	
}
