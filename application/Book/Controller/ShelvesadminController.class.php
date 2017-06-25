<?php
namespace Book\Controller;
use Common\Controller\AdminbaseController;

class ShelvesadminController extends AdminbaseController{
	
	protected $goods_model;
	protected $types_model;
	protected $term_relationships_model;
	protected $terms_model;
	
	function _initialize() {
		parent::_initialize();
		$this->goods_model = D("Book/Goods");
		$this->types_model = D("Book/GoodsType");
		$this->terms_model = D("Book/Terms");
		$this->term_relationships_model = D("Book/TermRelationships");
	}
	
	public function index(){
		$this->_lists(array("status"=>array('neq',3)));
		$this->_getTree();
		$this->display();
	}
	
	public function add(){
		$terms = $this->types_model->order(array("type_id"=>"asc"))->select();
		$term_id = I("get.term",0,'intval');
		$this->_getTermTree();
		$term=$this->types_model->where(array('type_id'=>$term_id))->find();
		$this->assign("term",$term);
		$this->assign("terms",$terms);
		$this->display();
	}
	
	public function add_book(){
		if (IS_POST) {
			if(empty($_POST['term'])){
				$this->error("请选择一个分类！");
			}
			if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
				foreach ($_POST['photos_url'] as $key=>$url){
					$photourl=sp_asset_relative_url($url);
					$_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
				}
			}

			$_POST['book']['cover'] = sp_asset_fetch_url($_POST['book']['cover']);
			$_POST['book']['create_time']=time();
// 			$_POST['book']['book_author']=get_current_admin_id();
			$article=I("post.book");
			$article['params']=json_encode($_POST['params'],JSON_UNESCAPED_UNICODE);      //不让存入的中文进行UNICODE转码
			$article['detail']=htmlspecialchars_decode($article['detail']);
			
			//暂时只选择1个分类
			foreach ($_POST['term'] as $mterm_id){
				$article['type_id'] = $mterm_id;
			}
			
			$result=$this->goods_model->add($article);
			if ($result) {
				$this->success("添加成功！");
			} else {
				$this->error("添加失败！");
			}
			 
		}
	}
	
	public function edit(){
		$id=  intval(I("get.id"));
		
// 		$term_relationship = M('TermRelationships')->where(array("object_id"=>$id,"status"=>1))->getField("term_id",true);
		$type_id = $this->goods_model->where(array("goods_id"=>$id))->getField('type_id',true);
		$this->_getTermTree($type_id);
		$terms=$this->types_model->select();
		$book=$this->goods_model->where(array("goods_id"=>$id))->find();
		$this->assign("book",$book);
		$this->assign("params",json_decode($book['params'],true));
		$this->assign("terms",$terms);
// 		$this->assign("term",$term_relationship);
		$this->display();
	}
	
	public function edit_book(){
		if (IS_POST) {
			if(empty($_POST['term'])){
				$this->error("请至少选择一个分类！");
			}
			$goods_id=intval($_POST['book']['goods_id']);
			
// 			$this->term_relationships_model->where(array("object_id"=>$goods_id,"term_id"=>array("not in",implode(",", $_POST['term']))))->delete();
			
			if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
				foreach ($_POST['photos_url'] as $key=>$url){
					$photourl=sp_asset_relative_url($url);
					$_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
				}
			}
			$_POST['book']['cover'] = sp_asset_fetch_url($_POST['book']['cover']);
			$_POST['book']['create_time']=strtotime($_POST['book']['create_time']);
			
			$article=I("post.book");
			$article['params']=json_encode($_POST['params'],JSON_UNESCAPED_UNICODE);
			$article['detail']=htmlspecialchars_decode($article['detail']);
			
			foreach ($_POST['term'] as $mterm_id){
				$article['type_id'] = $mterm_id;
			}
			
			$result=$this->goods_model->where(array('goods_id'=>$goods_id))->save($article);
			if ($result!==false) {
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}
	
	//排序
	public function listorders() {
		$status = parent::_listorders($this->goods_model);
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}
	
	private function _lists($where=array()){
		$term_id=I('request.term',0,'intval');
// 		$where['book_type']=array(array('eq',1),array('exp','IS NULL'),'OR');
		
		if(!empty($term_id)){
		    $where['type_id']=$term_id;
			$term=$this->types_model->where(array('type_id'=>$term_id))->find();
			$this->assign("term",$term);
		}
		
		$start_time=I('request.start_time');
		if(!empty($start_time)){
			$start_time = strtotime($start_time);
// 			dump(date('Y-m-d H:i:s', $start_time));
		    $where['create_time']=array(
		        array('EGT',$start_time)
		    );
		}
		
		$end_time=I('request.end_time');
		if(!empty($end_time)){
			$end_time = strtotime($end_time);
// 			dump(date('Y-m-d H:i:s', $end_time));
		    if(empty($where['create_time'])){
		        $where['create_time']=array();
		    }
		    array_push($where['create_time'], array('ELT',$end_time));
		}
		
		$keyword=I('request.keyword');
		if(!empty($keyword)){
			if($keyword == '销量优先') {
				$order['sale_num'] = 'DESC';
			}else {
				$keywords = explode(' ', $keyword);
				foreach($keywords as $i=>$vo) {
					$keywords[$i] = '%'.$vo.'%';
				}
				$where['CONCAT(name,params,detail)'] = array('like',$keywords,'AND');		//多字段模糊查询
				/*$where = array(									
					'name' => array('like',$keywords),
					'params' => array('like',$keywords),
					'detail' => array('like',$keywords),
					'_logic' => 'OR'
				);*/
			}
		}
			
		$this->goods_model->where($where);
		
		/*if(!empty($term_id)){
		    $this->goods_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
		}*/
		$count = $this->goods_model->count();
		$page = $this->page($count, 20);
		$order['listorder'] = 'DESC';
		$order['a.create_time'] = 'DESC';
		
		$this->goods_model
		->alias("a")
// 		->join("__USERS__ c ON a.book_author = c.id")
		->where($where)
		->limit($page->firstRow , $page->listRows)
		->order($order);
// 		if(empty($term_id)){
// 		    $this->goods_model->field('a.*,c.user_login,c.user_nicename');
// 		}else{
// 		    $this->goods_model->field('a.*,c.user_login,c.user_nicename,b.listorder,b.tid');
// 		    $this->goods_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
// 		}
		$books=$this->goods_model->select();
		
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("books",$books);
	}
	
	private function _getTree(){
		$term_id=empty($_REQUEST['term'])?0:intval($_REQUEST['term']);
		$result = $this->types_model->order(array("type_id"=>"asc"))->select();
		
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("Portal/AdminTerm/add", array("parent" => $r['type_id'])) . '">添加子类</a> | <a href="' . U("Portal/AdminTerm/edit", array("id" => $r['type_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("Portal/AdminTerm/delete", array("id" => $r['type_id'])) . '">删除</a> ';
			$r['visit'] = "<a href='#'>访问</a>";
			$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
			$r['id']=$r['type_id'];
			$r['parentid']=$r['parent'];
			$r['selected']=$term_id==$r['type_id']?"selected":"";
			$array[] = $r;
		}
		
		$tree->init($array);
		$str="<option value='\$id' \$selected>\$spacer\$name</option>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
	}
	
	private function _getTermTree($term=array()){
		$result = $this->types_model->order(array("type_id"=>"asc"))->select();
		
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("Portal/AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("Portal/AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("Portal/AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
			$r['visit'] = "<a href='#'>访问</a>";
			$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
			$r['id']=$r['type_id'];
			$r['parentid']=$r['parent'];
			$r['selected']=in_array($r['type_id'], $term)?"selected":"";
			$r['checked'] =in_array($r['type_id'], $term)?"checked":"";
			$array[] = $r;
		}
		
		$tree->init($array);
		$str="<option value='\$id' \$selected>\$spacer\$name</option>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
	}
	
	public function delete(){
		if(isset($_GET['id'])){
			$id = I("get.id",0,'intval');
			if ($this->goods_model->where(array('goods_id'=>$id))->save(array('inventory'=>0,'status'=>3)) !==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			
			if ($this->goods_model->where(array('goods_id'=>array('in',$ids)))->save(array('status'=>3))!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
	
	public function check(){
		if(isset($_POST['ids']) && $_GET["check"]){
		    $ids = I('post.ids/a');
			
			if ( $this->goods_model->where(array('goods_id'=>array('in',$ids)))->save(array('is_new'=>1)) !== false ) {
				$this->success("设置新品成功！");
			} else {
				$this->error("设置失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["uncheck"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->goods_model->where(array('goods_id'=>array('in',$ids)))->save(array('is_new'=>0)) !== false) {
				$this->success("取消设置成功！");
			} else {
				$this->error("取消设置失败！");
			}
		}
	}
	
	function top(){
		if(isset($_POST['ids']) && $_GET["top"]){
			$ids = I('post.ids/a');
			
			if ( $this->goods_model->where(array('goods_id'=>array('in',$ids)))->save(array('is_hot'=>1))!==false) {
				$this->success("设置热门成功！");
			} else {
				$this->error("设置失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["untop"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->goods_model->where(array('goods_id'=>array('in',$ids)))->save(array('is_hot'=>0))!==false) {
				$this->success("取消设置成功！");
			} else {
				$this->error("取消设置失败！");
			}
		}
	}
	
	function recommend(){
		if(isset($_POST['ids']) && $_GET["recommend"]){
			$ids = I('post.ids/a');
			
			if ( $this->goods_model->where(array('goods_id'=>array('in',$ids)))->save(array('is_recommend'=>1))!==false) {
				$this->success("推荐成功！");
			} else {
				$this->error("推荐失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["unrecommend"]){
		    $ids = I('post.ids/a');
		    
			if ( $this->goods_model->where(array('goods_id'=>array('in',$ids)))->save(array('is_recommend'=>0))!==false) {
				$this->success("取消推荐成功！");
			} else {
				$this->error("取消推荐失败！");
			}
		}
	}
	
	public function move(){
		if(IS_POST){
			if(isset($_GET['ids']) && $_GET['old_term_id'] && isset($_POST['term_id'])){
			    $old_term_id=I('get.old_term_id',0,'intval');
			    $term_id=I('post.term_id',0,'intval');
			    if($old_term_id!=$term_id){
			        $ids=explode(',', I('get.ids/s'));
			        $ids=array_map('intval', $ids);
			         
			        foreach ($ids as $id){
			            $this->term_relationships_model->where(array('object_id'=>$id,'term_id'=>$old_term_id))->delete();
			            $find_relation_count=$this->term_relationships_model->where(array('object_id'=>$id,'term_id'=>$term_id))->count();
			            if($find_relation_count==0){
			                $this->term_relationships_model->add(array('object_id'=>$id,'term_id'=>$term_id));
			            }
			        }
			        
			    }
			    
			    $this->success("移动成功！");
			}
		}else{
			$tree = new \Tree();
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$terms = $this->terms_model->order(array("path"=>"ASC"))->select();
			$new_terms=array();
			foreach ($terms as $r) {
				$r['id']=$r['term_id'];
				$r['parentid']=$r['parent'];
				$new_terms[] = $r;
			}
			$tree->init($new_terms);
			$tree_tpl="<option value='\$id'>\$spacer\$name</option>";
			$tree=$tree->get_tree(0,$tree_tpl);
			 
			$this->assign("terms_tree",$tree);
			$this->display();
		}
	}
	
	public function copy(){
	    if(IS_POST){
	        if(isset($_GET['ids']) && isset($_POST['term_id'])){
	            $ids=explode(',', I('get.ids/s'));
	            $ids=array_map('intval', $ids);
	            $uid=sp_get_current_admin_id();
	            $term_id=I('post.term_id',0,'intval');
	            $term_count=$terms_model=M('Terms')->where(array('term_id'=>$term_id))->count();
	            if($term_count==0){
	                $this->error('分类不存在！');
	            }
	            
	            $data=array();
	            
	            foreach ($ids as $id){
	                $find_book=$this->goods_model->field('name,cover')->where(array('goods_id'=>$id))->find();
	                if($find_book){
// 	                    $find_book['book_author']=$uid;
	                    $find_book['create_time']=date('Y-m-d H:i:s');
// 	                    $find_book['create_time']=date('Y-m-d H:i:s');
	                    $goods_id=$this->goods_model->add($find_book);
	                    if($goods_id>0){
	                        array_push($data, array('object_id'=>$goods_id,'term_id'=>$term_id));
	                    }
	                }
	            }
	            
	            if ( $this->term_relationships_model->addAll($data) !== false) {
	                $this->success("复制成功！");
	            } else {
	                $this->error("复制失败！");
	            }
	        }
	    }else{
	        $tree = new \Tree();
	        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
	        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
	        $terms = $this->terms_model->order(array("path"=>"ASC"))->select();
	        $new_terms=array();
	        foreach ($terms as $r) {
	            $r['id']=$r['term_id'];
	            $r['parentid']=$r['parent'];
	            $new_terms[] = $r;
	        }
	        $tree->init($new_terms);
	        $tree_tpl="<option value='\$id'>\$spacer\$name</option>";
	        $tree=$tree->get_tree(0,$tree_tpl);
	
	        $this->assign("terms_tree",$tree);
	        $this->display();
	    }
	}
	
	function recyclebin(){
		$this->_lists(array('status'=>array('eq',3)));
		$this->_getTree();
		$this->display();
	}
	
	function clean(){
		if(isset($_POST['ids'])){
			$ids = I('post.ids/a');
			$ids = array_map('intval', $ids);
			$status=$this->goods_model->where(array("id"=>array('in',$ids),'status'=>3))->delete();
			$this->term_relationships_model->where(array('object_id'=>array('in',$ids)))->delete();
			
			if ($status!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			if(isset($_GET['id'])){
				$id = I("get.id",0,'intval');
				$status=$this->goods_model->where(array("id"=>$id,'status'=>3))->delete();
				$this->term_relationships_model->where(array('object_id'=>$id))->delete();
				
				if ($status!==false) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
			}
		}
	}
	
	function restore(){
		if(isset($_GET['id'])){
			$id = I("get.id",0,'intval');
			if ($this->goods_model->where(array("id"=>$id,'status'=>3))->save(array("status"=>"1"))) {
				$this->success("还原成功！");
			} else {
				$this->error("还原失败！");
			}
		}
	}
	
	public function amazonISBN() {
		$isbn = I('post.isbn');
		if(!empty($isbn)) {
			$data = R('Util/PhpSpider/crawlAmazon', array($isbn));
			$data['status'] = 1;
			$this->ajaxReturn($data);
		}else {
			$this->error('请求失败');
		}
	}
	
	public function juheISBN() {
		$isbn = I('post.isbn');
		if(!empty($isbn)) {
			//配置您申请的appkey
			$appkey = "eb7eb5dcff087a63eb11f259be4e4dfc";
				
			//************1.ISBN查询书籍详情************
			$url = "http://feedback.api.juhe.cn/ISBN";
			$params = array(
					"sub" => $isbn,//ISBN
					"key" => $appkey,//应用APPKEY(应用详细页查询)
			);
			$paramstring = http_build_query($params);
			$content = $this->juhecurl($url,$paramstring);
			$result = json_decode($content,true);
			if($result){
				if($result['error_code']=='0'){
					$this->ajaxReturn($result['result']);
				}else{
					$this->error($result['error_code'].":".$result['reason']);
				}
			}else{
				$this->error('请求失败');
			}
		}else {
			$this->error('请求失败');
		}
	}
	
	/**
	 * 请求接口返回内容
	 * @param  string $url [请求的URL地址]
	 * @param  string $params [请求的参数]
	 * @param  int $ipost [是否采用POST形式]
	 * @return  string
	 */
	private function juhecurl($url,$params=false,$ispost=0){
		$httpInfo = array();
		$ch = curl_init();
	
		curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
		curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
		curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if( $ispost )
		{
			curl_setopt( $ch , CURLOPT_POST , true );
			curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
			curl_setopt( $ch , CURLOPT_URL , $url );
		}
		else
		{
			if($params){
				curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
			}else{
				curl_setopt( $ch , CURLOPT_URL , $url);
			}
		}
		$response = curl_exec( $ch );
		if ($response === FALSE) {
			//echo "cURL Error: " . curl_error($ch);
			return false;
		}
		$httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
		$httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
		curl_close( $ch );
		return $response;
	}
	
}