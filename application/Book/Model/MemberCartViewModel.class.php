<?php
namespace Book\Model;
use Think\Model\ViewModel;

class MemberCartViewModel extends ViewModel{
	public $viewFields = array(
		'MemberCart' => array('id','member_id','goods_id','num','status'),
		'Goods' => array('name','pre_price','now_price','cover','inventory','_on'=>'Goods.goods_id=MemberCart.goods_id'),
	);
}