<?php

/**
 *  CURL操作类，可用于模拟请求
 *  @author leshen <986992484@qq.com>
 *  @version 1.1
 *  usage:
 *	1.设置选项
 *  $options=array(
 *    'isReturn'=>true,   //将响应结果返回。如果不想获取源码而是想渲染页面，请设置为false
 *	  'isHeader'=>true,   //将响应头返回
 *   );
 *  $curl=new Curl($options);
 *  2.模拟一般的get请求
 *  $curl=new Curl();
 *  $url="https://www.baidu.com";
 *  var_dump($curl->curl_get($url));
 *  3.模拟需要盗链的get请求（查询四六级为例）
 *  $curl=new Curl();
 *  $url='http://www.chsi.com.cn/cet/query';
 *  $referer="http://www.chsi.com.cn/cet";
 *  $data=array('xm'=>'钟林生','zkzh'=>'360021161218718');
 *  $respn=$curl->curl_get_chain($url,$data,$referer);
 *  var_dump($respn);
 *  4.模拟post请求（查询四六级为例）
 *	 $data=array(
 *	 'name'=>'王勇平',
 *	 'province'=>'江西',
 *	 'school'=>'江西师范大学',
 *	 'type'=>'1'
 *	);
 *	$url='http://cet.zy62.com/query/2';
 *	$curl=new Curl();
 *	var_dump($curl->curl_post($url,$data));
 */

class Curl{

	/**
     * curl资源句柄
     * @var resource
     */
    private $curl;

    /*curl选项*/
    private $isReturn;   //是否将响应结果返回
    private $isHeader;   //是否将响应头返回
    private $timeout;    //超时时间
    private $userAgent;  //客户端代理
    private $verifyPeer; //是否终止cURL从服务端进行验证
    private $verifyHost; //检查服务器SSL证书中是否存在一个公用名

    /**
     * 构造方法，用于实例化一个curl对象
     */
    public function __construct($options=array()){
        /*初始化资源句柄*/
        $this->curl=curl_init();
        /*初始化curl选项*/
        $this->isReturn=isset($options['isReturn'])?$options['isReturn']:true;
        $this->isHeader=isset($options['isHeader'])?$options['isHeader']:false;
        $this->timeout=isset($options['timeout'])?$options['timeout']:30;
        $this->userAgent=isset($options['userAgent'])?$options['timeout']:
           isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']: 
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        $this->verifyPeer=isset($options['verifyPeer'])?$options['verifyPeer']:false;
        $this->verifyHost=isset($options['verifyHost'])?$options['verifyHost']:2;
    }   

	/**
     * 设置curl选项
     * @param  string   $url  	请求的url
     * @param  boolean  $ssl 	是否以https协议传输
     * @return void 
     */	
    private function setOption($url,$ssl){
		/*设置curl选项*/
		curl_setopt($this->curl, CURLOPT_URL, $url);//URL
		curl_setopt($this->curl, CURLOPT_USERAGENT, $this->userAgent);//userAgent，请求代理信息
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);//设置超时时间

		/*SSL相关*/
		if ($ssl) {
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER,$this->verifyPeer);//禁用后cURL将终止从服务端进行验证
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST,$this->verifyHost);//检查服务器SSL证书中是否存在一个公用名(common name)。
		}

	    /*响应结果*/
		curl_setopt($this->curl, CURLOPT_HEADER, $this->isHeader);//是否返回响应头
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $this->isReturn);//curl_exec()是否返回响应结果
    }

	/**
     * 执行curl请求
     * @return string  返回响应内容
     */	
    private function exec(){
		/*发出请求*/
		$response = curl_exec($this->curl);
		if (false === $response) {
			echo '<br>', curl_error($this->curl), '<br>';
			return false;
		}
		curl_close($this->curl);
		return $response;
    }

	/**
     * curl模拟post请求,返回响应的内容
     * @param  string   $url  		请求的url
     * @param  array    $data  		发送的数据，数组
     * @param  boolean  $ssl 		是否以https协议传输,默认为true
     * @return string   $response  	返回响应的内容
     */	
	public function curl_post($url, $data, $ssl=true) {
		/*设置选项*/
		$this->setOption($url,$ssl);

		/*处理post相关选项*/
		curl_setopt($this->curl, CURLOPT_POST, true);// 是否为POST请求
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);// 设置post的内容

		/*执行curl请求*/
		if($this->isReturn){
			return $this->exec();
		}
		$this->exec();
	}

	/**
     * curl模拟一般的get请求,返回响应的内容
     * @param  string   $url  		请求的url,可以带查询参数
     * @param  boolean  $ssl 		是否以https协议传输,默认为true
     * @return string   $response  	返回响应的内容
     */	
	public function curl_get($url,$ssl=true) {
		/*设置选项*/
		$this->setOption($url,$ssl);

		/*执行curl请求*/
		if($this->isReturn){
			return $this->exec();
		}
		$this->exec();
	}	

	/**
     * curl模拟需要盗链的get请求,返回响应的内容
     * @param  string   $url  		请求的url
     * @param  array    $data  		查询参数，数组形式，方法内会自动转换为字符串形式
     * @param  string   $referer 	盗链的url
     * @param  boolean  $ssl 		是否以https协议传输,默认为true
     * @return string   $response  	返回响应的内容
     */	
	public function curl_get_chain($url,$data,$referer,$ssl=true){
		/*设置选项*/
		$this->setOption($url,$ssl);
		$param='';
		foreach ($data as $k => $v) {
			$param.= urlencode($k).'='.urlencode($v).'&';
		}
		/*设置查询参数*/
    	curl_setopt($this->curl, CURLOPT_POST, 0);
  	    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $param);

		/*设置referer盗链*/
		curl_setopt($this->curl, CURLOPT_REFERER, $referer);

  	    /*执行curl请求*/
		if($this->isReturn){
			return $this->exec();
		}
		$this->exec();
	}
}








