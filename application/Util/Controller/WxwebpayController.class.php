<?php
namespace Util\Controller;
use com\unionpay\acp\sdk;
use Think\Controller;
/**
 * 网页支付接口
 * @author Zhong
 *
 */
class WebpayController extends Controller {

	/**
	 * 获取支付信息
	 * @param unknown $order_id
	 * @param unknown $type 0支付宝,1微信,2银联
	 */
	public function getCharge($order_id,$type,$openid=''){
		$order=D('Order')->where('order_id='.$order_id)->find();
		
		if(!$order||($order['status']!=0&&$order['status']!=11)){
			$res['code']=2000;
			$res['message']="订单不存在或者订单状态不对";
			return $res;
		}
		
		if($type==1){//微信支付
			import("Org.Pay.WxWeb.autoload");
			$wx_order=new \WxPayUnifiedOrder();
			if($order['type']==0){//正常订单
				$wx_order->SetOut_trade_no($order_id);
				$wx_order->SetBody("听说-".$order_id);
				$wx_order->SetAttach("0");
				$wx_order->SetTotal_fee($order['total_price']*100);
			}
			elseif($order['type']==1){
				if($order['status']==0){//预付款
					$wx_order->SetOut_trade_no("00000001".$order_id);
					$wx_order->SetBody("听说预付款-".$order_id);
					$wx_order->SetAttach("1");
					$wx_order->SetTotal_fee($order['prepaid_price']*100);
				}
				elseif($order['status']==11){//付尾款
					$wx_order->SetOut_trade_no("00000002".$order_id);
					$wx_order->SetBody("听说尾款-".$order_id);
					$wx_order->SetAttach("2");
					$wx_order->SetTotal_fee($order['lastpaid_price']*100);
				}
			}
			elseif($order['type']==3){//拼团订单
				$wx_order->SetOut_trade_no($order_id);
				$wx_order->SetBody("听说拼团-".$order_id);
				$wx_order->SetAttach("3");
				$wx_order->SetTotal_fee($order['total_price']*100);
			}
			
			$wx_order->SetOpenid($openid);
			$wx_order->SetTrade_type("JSAPI");

			$notify_url='http://'.$_SERVER['HTTP_HOST'].'/index.php/api/payhook/wx_web_hook';
			$wx_order->SetNotify_url($notify_url);

			$result=\WxPayApi::unifiedOrder($wx_order);
			
			if($result['return_code']!='SUCCESS'){
				$res['code']=2000;
				$res['message']=$result;
				return $res;
			}
			if($result['result_code']!='SUCCESS'){
				$res['code']=2000;
				$res['message']="交易失败:错误代码为".$result['err_code'];
				return $res;
			}
			
			//成功返回值
			$pay_params['appId']=$result['appid'];
			$pay_params['nonceStr']=$result['nonce_str'];
			$pay_params['signType']="MD5";
			$pay_params['timeStamp']=time()."";
			$pay_params['package']="prepay_id=".$result['prepay_id'];
			
			$app_params=new \AppPayParams();
			$app_params->FromArray($pay_params);
			$app_params->SetSign();
			
			$sing_array=$app_params->getArray();
			$sing_array['paySign']=$sing_array['sign'];
			unset($sing_array['sign']);
			$res['code']=0;
			$res['charge']=json_encode($sing_array);
		}
		elseif($type==0){//支付宝支付
			import("Org.Pay.Ali.autoload");
			$aop_client=new \AopClient();
			
			$biz_content['subject']="听说-".$order_id;
			$biz_content['body']="听说订单详情";
			$biz_content['timeout_express']="30m";
			$biz_content['out_trade_no']=$order_id."";
			$biz_content['total_amount']=$order['total_price']."";
			$biz_content['product_code']="QUICK_MSECURITY_PAY";
			
			$ali_data=$aop_client->get_app_pay_data($biz_content);
			$charge['order_string']=$ali_data;
			$res['charge']=$charge;
		}
		elseif($type==2){//银联支付
			import("Org.Pay.Union.autoload");
			$params = array(
					//以下信息非特殊情况不需要改动
					'version' => '5.0.0',                 //版本号
					'encoding' => 'utf-8',				  //编码方式
					'txnType' => '01',				      //交易类型
					'txnSubType' => '01',				  //交易子类
					'bizType' => '000201',				  //业务类型
					'frontUrl' =>  'http://'.$_SERVER['HTTP_HOST'].'/index.php/member/order',  //前台通知地址
					'backUrl' => 'http://'.$_SERVER['HTTP_HOST'].'/index.php/rapiv2/payhook/union_hook',	  //后台通知地址
					'signMethod' => '01',	              //签名方法
					'channelType' => '08',	              //渠道类型，07-PC，08-手机
					'accessType' => '0',		          //接入类型
					'currencyCode' => '156',	          //交易币种，境内商户固定156
			
					//TODO 以下信息需要填写
					'merId' => '826440172980001',		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
					//'orderId' => '00000000'.$order_id,	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
					'txnTime' => date("YmdHis"),	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
					//'txnAmt' => $order['total_price']*100,	//交易金额，单位分，此处默认取demo演示页面传递的参数
					// 		'reqReserved' =>'透传信息',        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据
			
					//TODO 其他特殊用法请查看 pages/api_05_app/special_use_purchase.php
			);
			
			if($order['type']==0){//正常订单
				$params['orderId']='00000000'.$order_id;
				$params['txnAmt']=$order['total_price']*100;
			}
			elseif($order['type']==1){
				if($order['status']==0){//预付款
					$params['orderId']='00000001'.$order_id;
					$params['txnAmt']=$order['prepaid_price']*100;
				}
				elseif($order['status']==11){//付尾款
					$params['orderId']='00000002'.$order_id;
					$params['txnAmt']=$order['lastpaid_price']*100;
				}
			}
			elseif($order['type']==3){//拼团订单
				$params['orderId']='00000003'.$order_id;
				$params['txnAmt']=$order['total_price']*100;
			}
			
			\com\unionpay\acp\sdk\AcpService::sign ( $params ); // 签名
			$url =\com\unionpay\acp\sdk\SDK_FRONT_TRANS_URL;
			
			$html_form =\com\unionpay\acp\sdk\AcpService::createAutoFormHtml ($params,$url);
			$res = $html_form;
			//echo $html_form;
		}
		
		
		return $res;
	}
}
