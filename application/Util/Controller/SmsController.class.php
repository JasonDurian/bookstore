<?php
namespace Util\Controller;
use Think\Controller;
import("Org.Util.alisms.TopSdk");
/**
 * 短信工具
 * @author asus
 *
 */
class SmsController extends Controller {
	
	private $app_key="23430749";
	private $app_secret="9c3181ffd369e630d1e8376b01a5b843";

	/**
	 * 发送注册验证短信
	 * @param unknown $code
	 * @param unknown $tel
	 */
	public function SendRegisterSms($code,$tel){

		$c = new \TopClient;
		$c->appkey = $this->app_key;
		$c->secretKey = $this->app_secret;
		$c->format='json';
		$req = new \AlibabaAliqinFcSmsNumSendRequest;
		$req->setExtend("123456");
		$req->setSmsType("normal");
		$req->setSmsFreeSignName("维家星");
		$map['code']=$code;
		$map['product']="维家星";
		$req->setSmsParam(json_encode($map));
		$req->setRecNum($tel);
		$req->setSmsTemplateCode("SMS_13061492");
		$resp = $c->execute($req);
		return $resp;
	}

   
   
}

?>
