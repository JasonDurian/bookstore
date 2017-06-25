<?php
namespace Book\Controller;
use Common\Controller\HomebaseController;

class CronController extends HomebaseController {
	protected $goods_model;
	protected $order_model; 
	protected $orderDetail_model;
	
	public function _initialize() {
		parent::_initialize();
		$this->goods_model = M('Goods');
		$this->order_model = M('Order');
		$this->orderDetail_model = M('OrderDetail');
	}
	
	/**
	 * 15分钟交易超时
	 */
	public function index() {
		$time = time();
		$where = array(
			'a.status'=>1,
			'a.create_time'=>array(
				'BETWEEN',array($time-1000,$time-900),
			),
		);
		$order = $this->order_model
		->alias('a')
		->field('goods_id,num')
		->join(array('__ORDER_DETAIL__ USING(order_id)'))
		->where($where)->select();
		$rst1 = $this->order_model->alias('a')->where($where)->setField(array('status'=>7,'end_time'=>$time));
// 		$rst3 = $this->orderDetail_model->alias('a')->where($where)->setField(array('status'=>7));     	//为了减轻数据库压力的话或许不用更新order_detail中的status
		if(!empty($order)) {
			$sql = "UPDATE yz_goods SET inventory = CASE goods_id ";
			$idArr = array();
			foreach ($order as $vo) {
				$sql .= sprintf("WHEN %d THEN inventory+%d ", $vo['goods_id'], $vo['num']);
				array_push($idArr, $vo['goods_id']);
			}
			$ids = implode(',', $idArr);			//把字段内容变成"x,x,x"的形式
			$sql .= "END WHERE goods_id IN ($ids)";
			$rst2 = $this->goods_model->execute($sql);
			if(($rst1 && $rst2)!==false) {
				echo '更新成功';
			}else {
				echo '更新失败';
			}
		}
	}
	
	/**
	 * 15天自动收货
	 */
	public function takeDelivery() {
	$time = time();
		$where = array(
			'a.status'=>3,
			'a.create_time'=>array(
				'BETWEEN',array($time-86400*16,$time-86400*15),
			),
		);
		$order = $this->order_model
		->alias('a')
		->field('goods_id,num')
		->join(array('__ORDER_DETAIL__ USING(order_id)'))
		->where($where)->select();
		$rst1 = $this->order_model->alias('a')->where($where)->setField(array('status'=>4));
// 		$rst3 = $this->orderDetail_model->alias('a')->where($where)->setField(array('status'=>4));     	//为了减轻数据库压力的话或许不用更新order_detail中的status
		if(!empty($order)) {
			$sql = "UPDATE yz_goods SET sale_num = CASE goods_id ";
			$idArr = array();
			foreach ($order as $vo) {
				$sql .= sprintf("WHEN %d THEN sale_num+%d ", $vo['goods_id'], $vo['num']);
				array_push($idArr, $vo['goods_id']);
			}
			$ids = implode(',', $idArr);			//把字段内容变成"x,x,x"的形式
			$sql .= "END WHERE goods_id IN ($ids)";
			$rst2 = $this->goods_model->execute($sql);
			if(($rst1 && $rst2)!==false) {
				echo '更新成功';
			}else {
				echo '更新失败';
			}
		}
	}
}