<?php
namespace Util\Controller;
/**
 * 内存锁
 * @author Zhong
 *
 */
class CacheLock {
	
	private $memcahe;
	
	private $memcahe_config=array(
		 'type'=>'memcache',
		 'host'=>'127.0.0.1',
		 'port'=>'11211',
 		 'prefix'=>'lock_',//前缀
		 'expire'=>10   //最多锁10s,防止阻塞
	);
	
	function __construct(){
// 		if ( extension_loaded('memcache') ) {
// 			$this->memcahe=  \Think\Cache::getInstance('memcache',$this->memcahe_config);
// 		}
// 		else{
			$this->memcahe=  \Think\Cache::getInstance('file',array( 'expire'=>10));
// 		}
 		
		
	}
	/**
	 * 加锁
	 * @param unknown $lock_name
	 */
	public function lock($lock_name,$expire = null){
		$lock=$this->memcahe->get($lock_name);
		if($lock){//有锁,别人正在使用
			return false;
		}
		//没人使用,加锁
		
		return $this->memcahe->set($lock_name,1,$expire);
	}
	
	public function unlock($lock_name){
		$this->memcahe->rm($lock_name);
	}
	
	
}

?>
