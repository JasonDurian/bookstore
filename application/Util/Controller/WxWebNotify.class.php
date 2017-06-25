<?php
namespace Util\Controller;
import("Org.Pay.WxWeb.autoload");

/**
 * 微信回调处理
 * @author Zhong
 *
 */
class WxWebNotify extends \WxPayNotify{
	
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		
		
		if($data['return_code']!='SUCCESS'||$data['result_code']!='SUCCESS'){
			\Think\Log::write( json_encode($data),'ERR','File',C('LOG_PATH').'paylog.log');
			return false;
		}
		$order=D('Order')->where('order_id='.$data['out_trade_no'])->find();
		if(!$order){
			\Think\Log::write( json_encode($data)."订单不存在",'ERR','File',C('LOG_PATH').'paylog.log');
			return false;
		}
		if($order['status']!=0&&$order['status']!=11){
			\Think\Log::write( json_encode($data)."订单状态不为0或者11,退出",'ERR','File',C('LOG_PATH').'paylog.log');
			return false;
		}
		
		$charge['id']=0;
		$charge['paid']=true;
		$charge['channel']='wx_pub';
		$charge['order_no']=$order['order_id'];
		$charge['amount']=$data['total_fee'];
		$charge['subject']=$data['out_trade_no'];
		$charge['time_paid']=strtotime($data['time_end']);
		$charge['transaction_no']=$data['transaction_id'];
		
		$order_handle=new \Common\Controller\OrderHandle();
		if($data['attach']==0)
			return $order_handle->handle_common_order($order,$charge);
		if($data['attach']==1)
			return $order_handle->handle_prepaid_order($order,$charge);
		if($data['attach']==2)
			return $order_handle->handle_lastpaid_order($order,$charge);
		if($data['attach']==3)
			return $order_handle->handle_group_order($order,$charge);
		
	}
	

	
}

?>
