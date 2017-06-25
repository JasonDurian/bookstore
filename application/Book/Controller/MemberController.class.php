<?php
namespace Book\Controller;
use Common\Controller\HomebaseController;

class MemberController extends HomebaseController{
	
	protected $users_model;
	protected $address_model;
	protected $order_model;
	protected $orderDetail_model;
	protected $orderAddr_model;
	protected $goods_model;
	
	private $allOrder;				//数据库中取出的订单
	private $orderBooks;			//订单详情
	
	public function _initialize() {
		parent::_initialize();
		$this->users_model = M('Member');
		$this->address_model = M('MemberAddress');
		$this->order_model = M('Order');
		$this->orderDetail_model = M('OrderDetail');
		$this->orderAddr_model = M('OrderAddress');
		$this->goods_model = M('Goods');
	}
	
	public function index() {
		if(sp_is_user_login()) {
			$user = session('user');
			$user_id = $user['id'];
			$username = $user['nick_name'];
			$useravatar = $user['avatar'];
			
			if($useravatar==NULL){
				$useravatar = 'avatar/default.png';
			}
			
			$wait['payment'] = $this->order_model->where(array('member_id'=>$user_id,'status'=>'1'))->count();
			$wait['delivery'] = $this->order_model->where(array('member_id'=>$user_id,'status'=>'2'))->count();
			$wait['goods'] = $this->order_model->where(array('member_id'=>$user_id,'status'=>'3'))->count();
			$wait['comment'] = $this->order_model->where(array('member_id'=>$user_id,'status'=>'4'))->count();
			
			$this->assign('username',$username);
			$this->assign('useravatar',$useravatar);
			$this->assign('wait',$wait);
			$this->display();
		} else {
			$this->redirect("StoreUser/Login/index");
		}
	}
	
	public function order() {
		if(sp_is_user_login()) {
			
			$this->myallOrder(1);
			
			$this->assign('books',$this->orderBooks);
			$this->assign('allOrder',$this->allOrder);
			$this->display();
		} else {
			$this->redirect("StoreUser/Login/index");
		}
	}
	
	public function waitpayment() {
		$this->myallOrder(0, 1);
		
		$this->assign('books',$this->orderBooks);
		$this->assign('allOrder',$this->allOrder);
		$this->display();
	}
	
	public function waitdelivery() {
		$this->myallOrder(0, 2);
		
		$this->assign('books',$this->orderBooks);
		$this->assign('allOrder',$this->allOrder);
		$this->display();
	}
	
	public function waitgoods() {
		$this->myallOrder(0, 3);
		
		$this->assign('books',$this->orderBooks);
		$this->assign('allOrder',$this->allOrder);
		$this->display();
	}
	
	public function waitcomment() {
		$this->myallOrder(0, 4);
		
		$this->assign('books',$this->orderBooks);
		$this->assign('allOrder',$this->allOrder);
		$this->display();
	}
	
	/**
	 * 根据订单状态和页数确定是为全部订单还是ajax返回还是其他分页
	 */
	private function myallOrder($page_num,$status) {
		$user_id = sp_get_current_userid();
		
		if(!empty($status)) {
			$this->allOrder = $this->order_model->where(array('member_id'=>$user_id,'status'=>$status))->order(array('create_time'=>'desc'))->select();
		}else {
			$this->allOrder = $this->order_model->where(array('member_id'=>$user_id))->page($page_num,8)->order(array('create_time'=>'desc'))->select();
		}
		foreach ($this->allOrder as $k=>$vo) {
			$this->allOrder[$k]['statusTip2'] = "";
			$this->allOrder[$k]['statusTip3'] = "";
			switch ($vo['status']) {
				case '1': {
					$this->allOrder[$k]['statusTip1'] = "等待付款";
					$this->allOrder[$k]['statusTip2'] = '<button class="white fr" onclick="orderOperation('.$vo['order_id'].','.$vo['status'].')">现在付款</button>';
					$this->allOrder[$k]['statusTip3'] = '<button class="bgw fr" onclick="orderCancel('.$vo['order_id'].')">取消交易</button>';
				}break;
				case '2': {
					$this->allOrder[$k]['statusTip1'] = "即将发货";
					$this->allOrder[$k]['statusTip2'] = '<button class="white fr" onclick="orderOperation('.$vo['order_id'].','.$vo['status'].')">提醒发货</button>';
				}break;
				case '3': {
					$this->allOrder[$k]['statusTip1'] = "正在来的路上";
					$this->allOrder[$k]['statusTip2'] = '<button class="white fr" onclick="orderOperation('.$vo['order_id'].','.$vo['status'].')">确认收货</button>';
				}break;
				case '4': {
					$this->allOrder[$k]['statusTip1'] = "期待您的评价";
					$this->allOrder[$k]['statusTip2'] = '<button class="white fr" onclick="orderOperation('.$vo['order_id'].','.$vo['status'].')">去评价</button>';
				}break;
				case '5': {
					$this->allOrder[$k]['statusTip1'] = "交易完成";
					$this->allOrder[$k]['statusTip3'] = '<button class="bgw fr" onclick="orderDelete('.$vo['order_id'].','.($k+($page_num-1)*8).')">删除订单</button>';
				}break;
				case '6': {
					$this->allOrder[$k]['statusTip1'] = "交易取消";
					$this->allOrder[$k]['statusTip3'] = '<button class="bgw fr" onclick="orderDelete('.$vo['order_id'].','.($k+($page_num-1)*8).')">删除订单</button>';
				}break;
				case '7': {
					$this->allOrder[$k]['statusTip1'] = "支付超时";
					$this->allOrder[$k]['statusTip3'] = '<button class="bgw fr" onclick="orderDelete('.$vo['order_id'].','.($k+($page_num-1)*8).')">删除订单</button>';
				}break;
				default:break;
			}
		
			$orderBook = $this->orderDetail_model
			->alias('a')
			->field('a.goods_id,a.pre_price,a.now_price,num,name,cover')
			->join(array('__GOODS__ USING(goods_id)'))
			->where(array('order_id'=>$vo['order_id']))
			->select();
			$this->orderBooks[] = $orderBook;
		}
		/*$allOrder = $this->order_model->alias('a')
		->field('a.order_id,total_price,a.status,a.create_time,num,name,pre_price,now_price,cover')
		->join(array('__ORDER_DETAIL__ b USING(order_id)','__GOODS__ c ON b.goods_id=c.goods_id'))
		->where(array('member_id'=>$user_id))->page(1,8)->select();
		
		if($vo['status']==1) {
			$payMinute=floor((time()-$vo['create_time'])%86400/60);     //计算订单支付时间是否超时
			if($payMinute>15) {
				$orderStatus = $this->order_model
				->where(array('order_id'=>$vo['order_id'],'member_id'=>$user_id))
				->setField(array('status'=>7,'end_time'=>time()));
				if($orderStatus!==false) {
					$allOrder[$k]['status'] = "支付超时";
				}
			}else {
				$allOrder[$k]['status'] = "等待付款";
			}
		}*/
	}
	
	public function favorite() {
		if(sp_is_user_login()) {
			$user_id = sp_get_current_userid();
			$favorite_model = M('MemberCollection');
			
			/*$myFavorite = $favorite_model->field('goods_id')
			->where(array('member_id'=>$user_id))
			->order(array('create_time'=>'desc'))
			->page(1,10)->select();
			foreach ($myFavorite as $vo) {
				$book = $this->goods_model
				->field('goods_id,name,pre_price,now_price,cover')
				->where(array('goods_id'=>$vo['goods_id']))
				->select();
				$books[] = $book[0];
			}*/
			
			$myFavorite = $favorite_model
			->alias('a')
			->field('a.goods_id,name,pre_price,now_price,cover')
			->join(array('__GOODS__ USING(goods_id)'))
			->where(array('member_id'=>$user_id))
			->order(array('id'=>'desc'))
			->page(1,10)->select();
			
			$this->assign('books',$myFavorite);
			$this->display();
		} else {
			$this->redirect("StoreUser/Login/index");
		}
	}
	
	public function deleteFav() {
		$book_id = I('post.product_id');
		$favorite_model = M('MemberCollection');
		$user_id = sp_get_current_userid();
		if(!empty($book_id)) {
			$where = array(
				'member_id'=>$user_id,
				'goods_id'=>$book_id,
			);
			$result = $favorite_model->where($where)->delete();
			if ($result) {
				$this->success('删除成功');
			} else {
				$this->error($favorite_model->getError());
			}
		} else {
			$this->error('异常，稍后再试');
		}
	}
	
	public function MemberCenter() {
		$user_id = sp_get_current_userid();
			
		$this->display();
	}
	
	public function borrow() {
		if(sp_is_user_login()) {
			$user_id = sp_get_current_userid();
			
			$this->display();
		} else {
			$this->redirect("StoreUser/Login/index");
		}
	}
	
	public function editMember() {
		$user = session('user');
// 		$user_id = $user['id'];
		
// 		$information = $this->users_model->field('nick_name,avatar,mobile')->where(array('member_id'=>$user_id))->find();	
		$this->assign('information',$user);
		$this->display();
	}
	
	public function editAvatar() {
		$user = session('user');
		$avatar = $user['avatar'];
		
		$this->assign('avatar',$avatar);
		$this->display();
	}
	
	public function saveAvatar() {
		$user = session('user');
		$user_id = $user['id'];
		
		$savepath='avatar/'.date('Ymd').'/';
		$config = array(
			'maxSize' => 2097152,
			'rootPath' => './'.C("UPLOADPATH"),
			'savePath' => $savepath,
			'saveName' => array('uniqid',''),
			'exts' => array('jpg', 'gif', 'png', 'jpeg'),
			'autoSub' => false,
			//'subName' => array('date','Ymd'),
		);
		$upload = new \Think\Upload($config);		// 实例化上传类
		$info = $upload->upload();
		$avatarPath = $info['photo']['savepath'].$info['photo']['savename'];
		if(!$info) {								// 上传错误提示错误信息
			$mydata['error'] = $this->error($upload->getError());
			$mydata['result'] = -1;
		}else{										// 上传成功 获取上传文件信息
			$oldAvatar = $user['avatar'];
// 			unlink(sp_get_asset_upload_path($oldAvatar));
// 			sp_delete_avatar($oldAvatar);
// 			$upload->getUploader()->del($oldAvatar);
			$qiniu = new \Think\Upload\Driver\Qiniu(sp_get_cmf_settings('storage')['Qiniu']);	// 实例化七牛上传驱动类
			$qiniu->qiniu->del($oldAvatar);				// 删除七牛中的原头像
			$rst = $this->users_model->where(array('member_id'=>$user_id))->setField(array('avatar'=>$avatarPath));
			if($rst!==false) {
				session('user.avatar',$avatarPath);	// 更新session中的用户头像信息
				$mydata['avatar'] = sp_get_asset_upload_path($avatarPath);	// 新头像保存路径
				$mydata['result'] = 1;
			}else {
				$mydata['error'] = $this->users_model->getError();
				$mydata['result'] = -1;
			}
		}
		
		$this->ajaxReturn($mydata, 'AJAX_UPLOAD');
	}
	
	public function testAvatar() {
		$photo = $_FILES;
		dump($photo);
	}
	
	public function editNiceName() {
		$this->display();
	}
	
	public function saveNicename() {
		$user_id = sp_get_current_userid();
	
		$rules = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('nick_name', '/^[\w\x{4e00}-\x{9fa5}]{1,16}$/u', '用户昵称格式不对！', 1 ),
		);
		
		$data = $this->users_model->validate($rules)->create();
		if($data === false){
			$this->error($this->users_model->getError());
		}
		
		$rst = $this->users_model->where(array('member_id'=>$user_id))->setField($data);
		if($rst !== false) {
			session('user.nick_name',$data['nick_name']);
			$this->success("修改成功");
		}else {
			$this->error($this->users_model->getError());
		}
		
	}
	
	public function manageAddress() {
		$user_id = sp_get_current_userid();
		
		$address = $this->address_model->where(array('member_id'=>$user_id))->order('addr_id DESC')->select();
		
		$this->assign('address',$address);
		$this->display();
	}
	
	/**
	 * 添加地址
	 */
	public function addAddress() {
		$this->display();
	}
	
	/**
	 * 保存地址
	 */
	public function saveAddress() {
		$user_id = sp_get_current_userid();
		
		$rules = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('contact', '/^[\w\x{4e00}-\x{9fa5}]{1,16}$/u', '联系人由中英文组成,最多16位,不能为空！', 1 ),
			array('mobile', '/^\d{1,20}$/', '手机号由数字组成,最多20位,不能为空！', 1 ),
			array('address', '/^.{1,60}$/', '详细地址最多60个字符,不能为空！', 1 ),
		);
		$data = $this->address_model->validate($rules)->create();
		
		if($data === false) $this->error($this->address_model->getError(),'',1);
		
		$addr_id = I('get.addrID', 0, 'intval');
		$region = explode('-',I('post.region'));
		if(empty($region[0])) $this->error('请选择所在地区','',1);

		$data['member_id'] = $user_id;
		$data['province'] = $region[0];
		$data['city'] = $region[1];
		$data['district'] = $region[2];
		$data['is_default'] = $data['is_default'] == 'on' ? 1 : 0;
		$data['create_time'] = time();
		
		$defaultCount = $this->address_model->where(array('member_id'=>$user_id,'is_default'=>'1'))->count();     //默认地址数量
		if($defaultCount == 0) {
			$data['is_default'] = 1;     //如果用户地址为空
		}elseif($data['is_default'] == 1) {			//如果已经有默认地址，还要设置默认地址
			$this->address_model->where(array('member_id'=>$user_id,'is_default'=>'1'))->setField(array('is_default'=>'0'));
		}
		
		$result = $this->address_model->where(array('addr_id'=>$addr_id, 'member_id'=>$user_id))->add($data);
		if($result !== false) {
			redirect(leuu('Member/manageAddress'));
		}else {
			$this->error('添加失败','',1);
		}
	}
	
	/**
	 * 修改默认地址
	 */
	public function defaultAddress() {
		$user_id = sp_get_current_userid();
		$addr_id = I('post.addrID');
		
		if(!empty($addr_id)) {
			$rst1 = $this->address_model->where(array('member_id'=>$user_id,'addr_id'=>$addr_id))->setField(array('is_default'=>'1'));
			$rst2 = $this->address_model->where(array('member_id'=>$user_id,'addr_id'=>array('neq',$addr_id)))->setField(array('is_default'=>'0'));
			if(($rst1 && $rst2) !== false) {
				$data['result'] = 1;
			}else {
				$data['result'] = -1;
		    }
		} 
		
		$this->ajaxReturn($data,'json');
	}
	
	/**
	 * 编辑地址
	 */
	public function editAddress() {
		$user_id = sp_get_current_userid();
		$addr_id = I('get.addrID', 0, 'intval');
		$address = $this->address_model->where(array('member_id'=>$user_id,'addr_id'=>$addr_id))->find();
		$this->assign($address);
		$this->display();
	}
	
	/**
	 * 保存修改后的地址
	 */
	public function modifyAddress() {
		$user_id = sp_get_current_userid();
		
		$rules = array(
			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
			array('contact', '/^[\w\x{4e00}-\x{9fa5}]{1,16}$/u', '联系人由中英文组成,最多16位！', 1 ),
			array('mobile', '/^\d{1,20}$/', '手机号由数字组成,最多20位！', 1 ),
			array('address', '/^.{1,60}$/', '详细地址最多60个字符！', 1 ),
		);
		$data = $this->address_model->validate($rules)->create();
		
		if($data === false) $this->error($this->address_model->getError(),'',1);
		
		$addr_id = I('get.addrID', 0, 'intval');
		$region = explode('-',I('post.region'));
		if(empty($region[0])) $this->error('请选择所在地区','',1);
		
		$data['province'] = $region[0];
		$data['city'] = $region[1];
		$data['district'] = $region[2];
		
		$result = $this->address_model->where(array('addr_id'=>$addr_id, 'member_id'=>$user_id))->save($data);
		if($result !== false) {
			redirect(leuu('Member/manageAddress'));
		}else {
			$this->error('修改失败','',1);
		}	
	}
	
	/**
	 * 删除收货地址
	 */
	public function deleteAddress() {
		$user_id = sp_get_current_userid();
		$addr_id = I('post.addr_id');
		$where = array(
			'addr_id'=>(int)$addr_id,
			'member_id'=>$user_id
		);
		if(!empty($addr_id)) {
			$is_default = $this->address_model->where($where)->getField('is_default');
			if($is_default == 1) $this->address_model->where(array('member_id'=>$user_id,'is_default'=>0))->limit(1)->setField('is_default',1);
			
			$result = $this->address_model->where($where)->delete();
			if($result !== false) $data['result'] = 1;
			else $data['result'] = -1;
		}else {
			$data['result'] = -1;
		}
		$this->ajaxReturn($data,'json');
	}
	
	/**
	 * 提交订单页面选择地址
	 */
	public function selectAddress() {
		$user_id = sp_get_current_userid();
		
		$address = $this->address_model->where(array('member_id'=>$user_id))->select();
		
		$this->assign('address',$address);
		$this->display();
	}
	
	public function getLimit() {
		$page_num = I("post.page");
		$user_id = sp_get_current_userid();
		$favorite_model = M('MemberCollection');
			
		/*$myFavorite = $favorite_model->field('goods_id')
		->where(array('member_id'=>$user_id))
		->order(array('create_time'=>'desc'))
		->page($page_num,10)->select();
		foreach ($myFavorite as $vo) {
			$book = $this->goods_model
			->field('goods_id,name,pre_price,now_price,cover')
			->where(array('goods_id'=>$vo['goods_id']))
			->select();
			$books[] = $book[0];
		}
		$res['noMore']=$myFavorite;*/
		
		$myFavorite = $favorite_model
		->alias('a')
		->field('a.goods_id,name,pre_price,now_price,cover')
		->join(array('__GOODS__ USING(goods_id)'))
		->where(array('member_id'=>$user_id))
		->order(array('id'=>'desc'))
		->page($page_num,10)->select();
		
		$res['result']=$myFavorite;
		$this->ajaxReturn($res, 'json');
	}
	
	public function orderLimit() {
		$page_num = I("post.page");
		
		$this->myallOrder($page_num);
		
		$res['result1']=$this->allOrder;
		$res['result2']=$this->orderBooks;
		$this->ajaxReturn($res, 'json');
	}
	
	public function loginOut() {
		session('user',null);
		$this->redirect("Index/index");
	}
}