<?php
namespace Book\Controller;
use Common\Controller\HomebaseController;

class ShoppingController extends HomebaseController{
	protected $goods_model;
	protected $cart_model;
	protected $order_model; 
	protected $orderDetail_model;
	protected $orderAddr_model;
	
	public function _initialize() {
		parent::_initialize();
		$this->goods_model = M('Goods');
		$this->cart_model = M('MemberCart');
		$this->order_model = M('Order');
		$this->orderDetail_model = M('OrderDetail');
		$this->orderAddr_model = M('OrderAddress');
	}
	
	public function index() {
		if(sp_is_user_login()) {
			$user_id = sp_get_current_userid();
// 			$join = '__GOODS__ b ON b.goods_id = a.goods_id';
// 			$books = $this->cart_model
// 			->alias('a')
// 			->union(array('where'=>array('goods_id'=>'a.goods_id'),'table'=>'yz_goods'))
// 			->where(array('member_id'=>$user_id))
// 			->order('create_time desc')
// 			->select(); //两表联合

			$Model = D("MemberCartView");
			$books = $Model
			->field('member_id,goods_id,num,status,name,pre_price,now_price,inventory,cover')
			->where(array('member_id'=>$user_id))
			->order(array('status'=>'DESC','id'=>'DESC'))
			->select();
			
			$length = count($books,COUNT_NORMAL);
			for ($i=0;$i<$length;$i++) {
				if(($books[$i]['inventory']-$books[$i]['num']) < 0) {
					if ($books[$i]['inventory'] <= 0) {		//库存为0的话设置为失效宝贝
						$books[$i]['status'] = 0;
						$field = 'status';
						$value = 0;
					}else {
						$books[$i]['num'] = $books[$i]['inventory'];
						$field = 'num';
						$value = $books[$i]['inventory'];
					}
					$rst = $this->cart_model->where(array('member_id'=>$books[$i]['member_id'],'goods_id'=>$books[$i]['goods_id']))->setField($field,$value);
					if ($rst === false) $this->error('哪里出了点错误，请再试一次...');
				}
			}
			
			$this->assign('books', $books);
			$this->display();
		} else {
			$this->redirect("StoreUser/Login/index");
		}
	}
	
	/**
	 * 由商品明细添加购物车
	 */
	public function addshopping() {
		$book_id = I("post.product_id");
		$user_id = sp_get_current_userid();
		
		if(sp_is_user_login()) {
			if(!empty($book_id)) {
				$where = array(
					'member_id'=>$user_id,
					'goods_id'=>$book_id,
				);
				$result = $this->cart_model->where($where)->find();   //查询购物车中的数据
				$inventory = $this->goods_model->where(array('goods_id'=>$book_id))->getField('inventory');
				
				if($inventory > 0) {
					if(!empty($result)) {
						$book_num = $result['num'];
						if($book_num < $inventory) {              //判断购物车中商品数是否超出库存
							$rst1 = $this->cart_model->where($where)->setInc('num');    //购物车数据表中的商品数量加一
							if($rst1!==false) {
								$data['code'] = 1;
							}else {
								$data['message'] = '添加失败';
								$data['code'] = -1;
							}
						}else {
							$data['message'] = '超出库存';
							$data['code'] = -1;
						}
					}else {
						$cartData = array(
								'member_id' => $user_id,
								'goods_id' => $book_id,
								'create_time' => time(),
								'num' => 1,
						);
						$rst2 = $this->cart_model->add($cartData);
						if($rst2!==false) {
							$data['code'] = 1;
						}else {
							$data['message'] = '添加失败';
							$data['code'] = -1;
						}
					}
				}else {
					$data['message'] = '正在补货...';
					$data['code'] = -1;
				}
			}else {
				$data['message'] = '没有数据';
				$data['code'] = -1;
			}
		}else {
			$data['message'] = '请先登录';
			$data['code'] = -2;
		}
		$this->ajaxReturn($data, 'json');
	}
	
	public function addFavorite() {
		$book_id = I('post.product_id');
		$myFav = I("post.fav");
		$user_id = sp_get_current_userid();
		$favorite_model = M('MemberCollection');
	
		if(sp_is_user_login()) {
			if((!empty($book_id))&&($myFav!='')) {
				$where = array(
					'member_id' => $user_id,
					'goods_id' => $book_id,
				);
				if($myFav==0) {
					$favDel = $favorite_model->where($where)->delete();
					if($favDel!==false) {    //删除时除了失败会返回false，删除0条记录会返回0
						$data['code'] = 1;
					} else {
						$data['message'] = '操作出错';
						$data['code'] = -2;
					}
				} else {
					$favData = array(
							'member_id' => $user_id,
							'goods_id' => $book_id,
							'create_time' => time(),
					);
					$favAdd = $favorite_model->add($favData);
					if($favAdd) {
						$data['code'] = 1;
					} else {
						$data['message'] = '操作出错';
						$data['code'] = -2;
					}
				}
			} else {
				$data['message'] = '没有数据';
				$data['code'] = -1;
			}
		}else {
			$data['message'] = '请先登录';
			$data['code'] = -2;
		}
		$this->ajaxReturn($data, 'json');
	}
	
	public function deleteShopping() {
		$book_id = I('post.product_id');
		$user_id = sp_get_current_userid();
		if(!empty($book_id)) {
			$where = array(
				'member_id'=>$user_id,
				'goods_id'=>$book_id,
			);
			$result = $this->cart_model->where($where)->delete();
			if ($result) {
				$data['result'] = 1;
			} else {
				$data['result'] = 2;
			}
		} else {
			$data['result'] = 3;
		}
		
		$this->ajaxReturn($data,'json');
	}
	
	/**
	 * 购物车数量操作
	 */
	public function operation() {
		$book_id = I("post.product_id");
// 		$operation_id = I("post.operation_id");
		$book_num = I("post.product_num");
		$user = session('user');
		$user_id = $user['id'];
		
		$where = array(
			'member_id'=>$user_id,
			'goods_id'=>$book_id,
		);
		if((!empty($book_num))&&(!empty($book_id))) {
			$inventory = $this->goods_model->where(array('goods_id'=>$book_id))->getField('inventory');
			if($book_num < $inventory) {              			//判断购物车中商品数是否超出库存
				$rst1 = $this->cart_model->where($where)->save(array('num'=>$book_num));    //购物车数据表中的商品数量加一
				if($rst1!==false) {
					$data['result'] = 1;
				}else {
					$data['result'] = -1;
// 					$data['addition'] = $this->cart_model->getError();
				}
			}else {
				$rst2 = $this->cart_model->where($where)->save(array('num'=>$inventory));    //当前商品数量设为最大库存
				if($rst2==false) {
					$data['result'] = 1;
				}else {
					$data['result'] = -1;
				}
				$data['book_num'] = $inventory;
				$data['result'] = -2;
			}
			/*if($operation_id == 'myMinus') {
				$this->cart_model->where($where)->setDec('num');
				$data['result'] = 1;
			} else {
				$result = $this->cart_model->field('num')->where($where)->find();
				$book_num = $result['num'];
				$inventory = $this->goods_model->field('inventory')->where(array('goods_id'=>$book_id))->find();
				if($book_num < $inventory['inventory']) {              			//判断购物车中商品数是否超出库存
					$rst1 = $this->cart_model->where($where)->setInc('num');    //购物车数据表中的商品数量加一
					if($rst1!==false) {
						$data['result'] = 1;
					}else {
						$data['result'] = -1;
					}
				}else {
					$data['book_num'] = $book_num;
					$data['result'] = -2;
				}
			}*/
		} else {
			$data['book_num'] = $this->cart_model->where($where)->getField('num');
			$data['result'] = -1;
		}
		$this->ajaxReturn($data,'json');
	}
	
	/**
	 * 存储用户从购物车提交的数据并转到确认订单页面
	 */
	public function bookpay() {
		session('products',I('post.goods_ids'));      //把数据存放在session中，更换地址时需要用到
		$user_id = sp_get_current_userid();
	
		$products = session('products');
		$addrID = I('get.addrID');   //从selectAddress中获取addr_id
		$address_model = M('MemberAddress');
		
		if (!empty($products)) {
			
			/*$books = $this->goods_model->field('goods_id,name,cover,now_price,pre_price')->where(array('goods_id'=>array('in',$products)))->select();  //按主键从小到大的顺序查询
			$books_num = $this->cart_model->field('goods_id,num')
						->where(array('member_id'=>$user_id,'goods_id'=>array('in',$products)))
						->order('create_time desc')->select();			//查询顺序要和购物车查询时的顺序一致
			sort($books_num);*/
			
			$books = $this->goods_model
						->alias('a')
						->field('goods_id,name,cover,now_price,pre_price,inventory,num')
						->join(array('__MEMBER_CART__ b USING(goods_id)'))
						->where(array('b.member_id'=>$user_id,'goods_id'=>array('in',$products)))
						->order('a.goods_id DESC')->select();			//查询顺序要和购物车查询时的顺序一致
						
			$length = count($books,COUNT_NORMAL);
			for ($i=0;$i<$length;$i++) {
				if(($books[$i]['inventory']-$books[$i]['num']) < 0) $this->error('宝贝数量不够了，正在补货...',leuu('Shopping/index'),2);
			}
			//在订单中更换地址
			if(!empty($addrID)) {
				$defaultAddr = $address_model->where(array('member_id'=>$user_id,'addr_id'=>$addrID))->select();
			}else {
				$defaultAddr = $address_model->where(array('member_id'=>$user_id,'is_default'=>'1'))->select();
			}
			
			$this->assign('books',$books);
			$this->assign('defaultAddr',$defaultAddr[0]);
			$this->display();
		}
	}
	
	/**
	 * 提交订单
	 */
	public function submitOrder() {
		$user_id = sp_get_current_userid();
		$invoice = I('post.invoice');								//是否需要发票
		
		$rules = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('remark', '/^.{0,100}$/', '备注最多100个字符！', 1 ),
		);
		$data = $this->order_model->validate($rules)->create();
		if($data === false) $this->error($this->order_model->getError(),'',1);
		
		$details_data = $this->orderDetail_model->create();
		$address_data = $this->orderAddr_model->create();
		
		//order参数
// 		$order_sn = $user_id.time().rand(10,99);					//商品条码
		$order_sn = sp_get_order_sn();
		$create_time = time();
		$remark = ($invoice == 'on' ? '[*需要发票]' : '').$data['remark'];
		$data['order_sn'] = $order_sn;
		$data['member_id'] = $user_id;
		$data['remark']	= $remark;
		$data['create_time'] = $create_time;
		$detail_length = count($details_data['goods_id']);			//商品个数
		
		//额外的操作
		$cartRsts = $this->cart_model->where(array('goods_id'=>array('in',$details_data['goods_id']),'member_id'=>$user_id))->delete();		//删除购物车物品
		if($cartRsts == 0) $this->error('重复操作',leuu('Member/order'),1);										//解决页面返回后可以再次提交订单的bug
		
		$orderRst = $this->order_model->add($data);					//创建order，创建成功返回当前主键值
		
		if($orderRst) {
			$address_data['order_id'] = $orderRst;
			$address_data['create_time'] = $create_time;
			$addressRst = $this->orderAddr_model->add($address_data);	//把地址等信息加入订单地址表
			
			//批量减少字段
			$ids = implode(',', $details_data['goods_id']);			//把字段内容变成"x,x,x"的形式
			$sql = "UPDATE yz_goods SET inventory = CASE goods_id ";
			for ($i = 0; $i < $detail_length; $i++) {
				$sql .= sprintf("WHEN %d THEN inventory-%d ", $details_data['goods_id'][$i], $details_data['num'][$i]);
				//order_detail参数
				$dataList[] = array(
					'order_id' => $orderRst,
					'goods_id' => $details_data['goods_id'][$i],
					'num' => $details_data['num'][$i],
					'pre_price' => $details_data['pre_price'][$i],
					'now_price' => $details_data['now_price'][$i],
					'create_time' => $create_time
				);
			}
			$sql .= "END WHERE goods_id IN ($ids)";
			
			$goodsRst = $this->goods_model->execute($sql);					//批量减少库存
			$detailRst = $this->orderDetail_model->addAll($dataList);		//批量添加数据
			if(($goodsRst && $detailRst && $addressRst) === false) $this->error('操作出错',leuu('Member/order'),1);
			
			redirect(leuu('Shopping/orderDetail', array('orderID' => $orderRst)));
		}else {
			$this->order_model->getError();
		}
		
		/*$products = I("post.products");
		$productsPrice = I("post.productsPrice");
		$addrID = I("post.addrID");
		$payStatus = I("post.myStatus");
		$remark = I("post.myRemark");
		$payChannel = I("post.myChannel");
		
		$user_id = sp_get_current_userid();
		
		if (!empty($addrID)) {
// 			$order_model->create();
			$order_sn = $user_id.time().rand(10,99);
			$orderData = array(
				'order_sn'=>$order_sn,
				'member_id'=>$user_id,
				'addr_id'=>$addrID,
				'total_price'=>$productsPrice,
				'status'=>$payStatus,
				'remark'=>$remark,
				'pay_channel'=>$payChannel,
				'create_time'=>time(),
			);
			$orderRst = $this->order_model->add($orderData);
			if($orderRst) {
				$order_id = $this->order_model->where(array('order_sn'=>$order_sn))->getField('order_id');
				foreach ($products as $id) {
					$books = explode("-",$id);
					$data = array(
						'order_id'=>$order_id,
						'goods_id'=>$books[0],
						'num'=>$books[1],
						'price'=>$books[2],
// 						'status'=>$payStatus,
						'create_time'=>time(),
					);
					$rsts = $this->orderDetail_model->add($data);
					$cartRst = $this->cart_model->where(array('goods_id'=>$books[0],'member_id'=>$user_id))->delete();
					$goodsRst = $this->goods_model->where(array('goods_id'=>$books[0]))->setDec('inventory');
					if($rsts && $goodsRst && $cartRst) {
						$result['result'] = 1;
					}else {
						$result['result'] = -1;
					}
				}
			}else {
				$result['result'] = -1;
			}
		}else {
			$result['result'] = -1;
		}
		$result['order_id'] = $order_id;
		$this->ajaxReturn($result,'json');*/
	}
	
	/**
	 * 点击查看订单详情
	 */
	public function orderDetail() {
		$orderID = I("get.orderID",0,'intval');
		$user_id = sp_get_current_userid();
		$address_model = M('MemberAddress');
		
		if(!empty($orderID)) {
			$orderDetail = $this->order_model->where(array('order_id'=>$orderID,'member_id'=>$user_id))->find();
			$orderAddr = $this->orderAddr_model->where(array('order_id'=>$orderID))->find();
			
			$orderGoods = $this->orderDetail_model
			->alias('a')
			->field('a.goods_id,a.pre_price,a.now_price,num,name,cover')
			->join(array('__GOODS__ USING(goods_id)'))
			->where(array('order_id'=>$orderID))
			->select();
			
			/*if($orderDetail[0]['status']==7) {  				//设置订单结束时间
				$orderStatus = $this->order_model
				->where(array('order_id'=>$orderID,'member_id'=>$user_id))
				->setField(array('end_time'=>time()));
				$orderDetail[0]['end_time'] = time();
				
				$payMinute=floor((time()-$orderDetail[0]['create_time'])%86400/60);     //计算订单支付时间是否超时
				if($payMinute>15) {
					$orderStatus = $this->order_model
					->where(array('order_id'=>$orderID,'member_id'=>$user_id))
					->setField(array('status'=>7,'end_time'=>time()));
					if($orderStatus!==false) {
						$orderDetail[0]['status'] = 7;
						$orderDetail[0]['end_time'] = time();
					}
				}
			}*/
			
			$this->assign("orderDetail",$orderDetail);
			$this->assign("orderAddr",$orderAddr);
			$this->assign("books",$orderGoods);
		}
		$this->display();
	}
	
	/**
	 * 订单详情更新 需要考虑一直停留在订单详情页面的情况
	 */
	public function updateStatus() {
		$orderID = I("post.orderID");
		$payStatus = I("post.myStatus");
		$user = session('user');
		$user_id = $user['id'];
		$end_time = 0;        //结束时间
// 		$payMinute = 0;		  //支付时间

		if((!empty($orderID))&&(!empty($payStatus))) {
			switch ($payStatus) {
				case 1 :{								//交易超时，库存加交易数量
					$payStatus = $this->order_model->where(array('order_id'=>$orderID,'member_id'=>$user_id))->getField('status');      //如果超时，不再设置为等待付款的状态，而是交易超时
					//$payStatus = $statusRst['status'];
					/*if($statusRst == 7) {
						$goods_id = $this->orderDetail_model->field('goods_id')->where(array('order_id'=>$orderID,'member_id'=>$user_id))->select();
						foreach ($goods_id as $vo) {
							$rst = $this->goods_model
							->where(array('goods_id'=>$vo['goods_id']))
							->setInc('inventory');
							if($rst!==false) {
								$result['result'] = 1;
							}else {
								$result['result'] = -1;
							}
						}
						$payStatus = 7;
						$end_time = time();
					}
					$payMinute=floor((time()-$orderTime['create_time'])%86400/60);     //计算订单支付时间是否超时
					if($payMinute>15) {
						$payStatus = 7;
						$end_time = time();
					}*/
				}break;
				case 4 :{                                              //已经确认收货，销量加交易数量（系统默认15天自动收货）
					$goods = $this->orderDetail_model->field('goods_id,num')->where(array('order_id'=>$orderID))->select();
					$sql = "UPDATE yz_goods SET sale_num = CASE goods_id ";
					$idArr = array();
					foreach ($goods as $vo) {
						$sql .= sprintf("WHEN %d THEN sale_num+%d ", $vo['goods_id'], $vo['num']);
						array_push($idArr, $vo['goods_id']);
					}
					$ids = implode(',', $idArr);			//把字段内容变成"x,x,x"的形式
					$sql .= "END WHERE goods_id IN ($ids)";
					$rst = $this->goods_model->execute($sql);
					$result['result'] = $rst!==false ? 1 : -1;
				}break;
// 				case 5 :{												//交易完成
// 					$end_time = time();
// 				}break;
				case 6 :{												//交易取消，库存加交易数量
					$goods = $this->orderDetail_model->field('goods_id,num')->where(array('order_id'=>$orderID))->select();
					$sql = "UPDATE yz_goods SET inventory = CASE goods_id ";
					$idArr = array();
					foreach ($goods as $vo) {
						$sql .= sprintf("WHEN %d THEN inventory+%d ", $vo['goods_id'], $vo['num']);
						array_push($idArr, $vo['goods_id']);
					}
					$ids = implode(',', $idArr);			//把字段内容变成"x,x,x"的形式
					$sql .= "END WHERE goods_id IN ($ids)";
					$rst = $this->goods_model->execute($sql);
					$result['result'] = $rst!==false ? 1 : -1;
					$end_time = time();
				}break;
				default:break;
			}
			$orderStatus1 = $this->order_model
			->where(array('order_id'=>$orderID,'member_id'=>$user_id))
			->setField(array('status'=>$payStatus,'end_time'=>$end_time));
// 			$orderStatus2 = $this->orderDetail_model				//更新订单详情中的status
// 			->where(array('order_id'=>$orderID))->setField(array('status'=>$payStatus));
			if($orderStatus1!==false) {
				$result['result'] = 1;
			}else {
				$result['result'] = -1;
			}
		}else {
			$result['result'] = -1;
		}
		$this->ajaxReturn($result,'json');
	}
	
	public function deleteOrder() {
		$order_id = I("post.orderID");
		
		if(!empty($order_id)) {
			$result1 = $this->order_model->where(array('order_id'=>$order_id))->delete();
// 			$result2 = $this->orderDetail_model->where(array('order_id'=>$order_id))->delete();        //用外键删除
			if($result1) {
				$data['result'] = 1;
			} else {
				$data['result'] = -1;
			}
		} else {
			$data['result'] = -1;
		}
		$this->ajaxReturn($data,'json');
	}
	
	/**
	 * 发表评论
	 */
	public function releaseComm() {
		$orderID = I("get.orderID",0,'intval');
		$user_id = sp_get_current_userid();
		
		if(!empty($orderID)) {
			$orderGoods = $this->orderDetail_model
			->alias('a')
			->field('order_id,a.goods_id,cover')
			->join(array('__GOODS__ USING(goods_id)'))
			->where(array('order_id'=>$orderID))
			->select();
			
			$this->assign("books",$orderGoods);
		}
		$this->display();
	}
	
	/**
	 * 保存评论
	 */
	public function saveComm() {
		$comments_model = M('GoodsComments');
		$user_id = sp_get_current_userid();
		
		$data = $comments_model->create();
		$order_id = I('post.order_id',0,'intval');
		$orderStatus = $this->order_model->where(array('order_id'=>$order_id))->getField('status');
		
		if($orderStatus == '4') {
			$length = count($data['goods_id']);
			$type = $data['type'] == 'on' ? 0 : 1;       //是否匿名 1为实名
			$time = time();
				
			for ($i = 0; $i < $length; $i++) {
				$results[] = array(
					'member_id' => $user_id,
					'goods_id' => $data['goods_id'][$i],
					'content' => $data['content'][$i],
					'type' => $type,
					'score' => $data['score'][$i],
					'create_time' => $time
				);
			}
			
			$rst1 = $comments_model->addAll($results);			//增加评论数据		
			if($rst1 === false) $comments_model->getError();
			
			$rst2 = $this->order_model->where(array('order_id'=>$order_id))->setField(array('status'=>5,'end_time'=>$time));
			if($rst2 === false) {
				$this->order_model->getError();
			}else {
				$this->redirect(leuu('Shopping/orderDetail',array('orderID'=>$order_id)));
			}
		}else {
			$this->error('已评价',leuu('Member/waitcomment'),1);
		}
	}
	
	/**
	 * 后台点击查看订单详情
	 */
	public function adminDetail() {
		$orderID = I("get.orderID",0,'intval');
		// 		$user_id = sp_get_current_userid();
		$address_model = M('MemberAddress');
	
		if(!empty($orderID)) {
			$orderDetail = $this->order_model->where(array('order_id'=>$orderID))->find();
			$orderAddr = $this->orderAddr_model->where(array('order_id'=>$orderID))->find();
	
			$orderGoods = $this->orderDetail_model
			->alias('a')
			->field('a.goods_id,a.pre_price,a.now_price,num,name,cover')
			->join(array('__GOODS__ USING(goods_id)'))
			->where(array('order_id'=>$orderID))
			->select();
	
			$this->assign("orderDetail",$orderDetail);
			$this->assign("orderAddr",$orderAddr);
			$this->assign("books",$orderGoods);
		}
		$this->display();
	}
	
	/*public function testOrder() {
		$goods = $this->orderDetail_model->field('goods_id,num')->where(array('order_id'=>122))->select();
		$sql = "UPDATE yz_goods SET sale_num = CASE goods_id ";
		$idArr = array();
		foreach ($goods as $vo) {
			$sql .= sprintf("WHEN %d THEN sale_num+%d ", $vo['goods_id'], $vo['num']);
			array_push($idArr, $vo['goods_id']);
		}
		$ids = implode(',', $idArr);			//把字段内容变成"x,x,x"的形式
		$sql .= "END WHERE goods_id IN ($ids)";
		dump($sql);
		//$rst = $this->goods_model->execute($sql);
	}*/
}