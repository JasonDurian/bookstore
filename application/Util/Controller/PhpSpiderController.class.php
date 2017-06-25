<?php

namespace Util\Controller;

use Think\Controller;

/**
 * php爬虫接口
 * 
 * @author Jason
 *        
 */
ini_set ( "memory_limit", "1024M" );
vendor ( 'PHPSpider.core.init' );

class PhpSpiderController extends Controller {
	
	public function crawlAmazon($keyword='') {
		
		// "https://www.amazon.cn/s/ref=nb_sb_noss?__mk_zh_CN=%E4%BA%9A%E9%A9%AC%E9%80%8A%E7%BD%91%E7%AB%99&url=search-alias%3Dstripbooks&field-keywords={$ISBN}&rh=n%3A658390051%2Ck%3A7508672062"
		/*$configs = array (
				'log_type' => 'error,info', // 只记录普通和错误日志
				'log_file' => './data/spiderdata/spider.log', // 记录日志地址
				'max_fields' => 1, // 最多读取的字段数
				'name' => 'Amazon',
				'domains' => array (
						'amazon.cn',
						'www.amazon.cn' 
				),
				'scan_urls' => array (
						'https://www.amazon.cn/s/ref=nb_sb_noss?__mk_zh_CN=%E4%BA%9A%E9%A9%AC%E9%80%8A%E7%BD%91%E7%AB%99&url=search-alias%3Dstripbooks&field-keywords=9787108042453' 
				),
				'content_url_regexes' => array (
						'https://www.amazon.cn/s/ref=nb_sb_noss?__mk_zh_CN=%E4%BA%9A%E9%A9%AC%E9%80%8A%E7%BD%91%E7%AB%99&url=search-alias%3Dstripbooks&field-keywords=9787108042453' 
				),
				'list_url_regexes' => array (
						'https://www.amazon.cn/s/ref=nb_sb_noss?__mk_zh_CN=%E4%BA%9A%E9%A9%AC%E9%80%8A%E7%BD%91%E7%AB%99&url=search-alias%3Dstripbooks&field-keywords=9787108042453' 
				),
				'fields' => array (
					array (
						// 抽取内容页的文章内容
						'name' => "book_href",
						'selector' => "//a[@class='a-link-normal s-access-detail-page a-text-normal']",
						'required' => true 
					)
				// array(
				// // 抽取内容页的文章作者
				// 'name' => "article_author",
				// 'selector' => "//div[contains(@class,'author')]//h2",
				// 'required' => true
				// ),
				 
				)
		);
		
		$spider = new \phpspider ( $configs );
		$spider->start ();*/
		
		$domain = "https://www.amazon.cn";
		$url = "https://www.amazon.cn/s/ref=nb_sb_noss?__mk_zh_CN=%E4%BA%9A%E9%A9%AC%E9%80%8A%E7%BD%91%E7%AB%99&url=search-alias%3Dstripbooks&field-keywords={$keyword}";
		function disguise_curl($url) {
			$curl = curl_init ();
			
			// Setup headers - I used the same headers from Firefox version 2.0.0.6
			// below was split up because php.net said the line was too long. :/
			$header [0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
			$header [0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
			$header [] = "Cache-Control: max-age=0";
			$header [] = "Connection: keep-alive";
			$header [] = "Keep-Alive: 300";
			$header [] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
			$header [] = "Accept-Language: en-us,en;q=0.5";
			$header [] = "Pragma: "; // browsers keep this blank.
			
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
			curl_setopt( $curl, CURLOPT_ENCODING, 'gzip' );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
// 			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 );
			curl_setopt( $curl, CURLOPT_HEADER, true );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false);
// 			curl_setopt( $curl, CURLOPT_USERAGENT, "phpspider-requests/1.2.0" );
// 			curl_setopt( $curl, CURLOPT_TIMEOUT, 15);
// 			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1);
			// 在多线程处理场景下使用超时选项时，会忽略signals对应的处理函数，但是无耐的是还有小概率的crash情况发生
// 			curl_setopt( $curl, CURLOPT_NOSIGNAL, true);
			
			
// 			curl_setopt ( $curl, CURLOPT_REFERER, 'http://www.google.com' );
// 			curl_setopt ( $curl, CURLOPT_AUTOREFERER, true );
			
			$html = curl_exec ( $curl ); // execute the curl command
			curl_close ( $curl ); // close the connection
			
			return $html; // and finally, return $html
		}
		
		/*function curl_get_html($url){
			header('Content-Type: text/html; charset=utf-8');
			$cookie_file = dirname(__FILE__).'/cookie.txt';
			//先获取cookies并保存
			$ch = curl_init('www.amazon.cn'); //初始化
			curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回字符串，而非直接输出
			curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
			curl_exec($ch);
			curl_close($ch);
			//使用上面保存的cookies再次访问
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		}*/
		
		$html = disguise_curl( $url );
		
// 		$html = \requests::get($url);
		
		$selector = "//a[contains(@class, 'a-link-normal') and contains(@class, 's-access-detail-page') and contains(@class, 'a-text-normal')]/@href";
		$selectedUrl = \selector::select($html, $selector);
		
		$selector = "//a[contains(@class, 'a-link-normal') and contains(@class, 'a-text-normal')]/img/@src";
		$imgSrc = \selector::select($html, $selector);

// 		$matches = array();
// 		preg_match('/Location:(.*?)\n/', $html, $matches);
// 		$firstAppear = strpos($matches[1], 's');
// 		$replacedUrl = substr_replace($matches[1], '', $firstAppear, 1);
// 		$lastUrl = str_replace(' ', '', $replacedUrl);
		
		$nextHtml = disguise_curl( $selectedUrl[0] );
		
		$selector = "//span[@id='productTitle']";
		$title = \selector::select($nextHtml, $selector);
		
		$selector = "//span[contains(@class, 'author')]/a";
		$author = \selector::select($nextHtml, $selector);
		
// 		$selector = "//img[@id='imgBlkFront']/@src";
// 		$imgSrc = \selector::select($nextHtml, $selector);
		
		$selector = "//td[@class='bucket']/div[@class='content']/ul/li[1]/text()";
		$press = \selector::select($nextHtml, $selector);
// 		$pressArr = array('<b>出版社:</b> '=>'');
// 		$lastPress = strtr($press, $pressArr);

// 		$selector = "//div[contains(@class, 's-content')]/h3";
// 		$conTitles = \selector::select($nextHtml, $selector);

// 		$selector = "//div[contains(@class, 's-content')]/p";
// 		$conPieces = \selector::select($nextHtml, $selector);

// 		$contents = '';
// 		foreach ($conTitles as $k => $val) {
// 			$conPiece = str_replace(' ', '', $conPieces[$k]);
// 			$contents .= $val.'</br>'.$conPiece;
// 		}

		$selector = "//div[@id='s_contents']/@descurl";
		$contentsUrl = $domain . \selector::select($nextHtml, $selector);
		
		$lastHtml = disguise_curl( $contentsUrl );
		
		$selector = "//div[@id='s_contents']";
		$contents = \selector::select($lastHtml, $selector);
		
		$data = array(
			'title'    => $title,
			'author'   => $author,
			'imgUrl'   => $imgSrc[0],
			'press'    => $press,
// 			'conTitles'=> $conTitles,
			'contents' => $contents
		);
		
		return $data;
// 		dump($data);
	}
	
	public function crawlJD() {
		
		$url = 'https://search.jd.com/Search?keyword=9787550272095&enc=utf-8';
		$html = \requests::get($url);

		$selector = "//a[contains(@class, 'a-link-normal') and contains(@class, 'a-link-normal')]/@href";
// 		$title = \selector::select($html, $selector);
		dump($html);
		
	}
	
	/*public function test() {
		$str = "<b>Hello world</b>";
		$arr = array("<b>" => "Hi", "world" => "earth");
		$next = strtr($str,$arr);
		echo $next;
	}*/
}