<?php

/**
 *  查询四六级成绩类
 *  @author leshen <986992484@qq.com>
 *  @version 1.1
 */

class Cet{

   /**
     * 通过姓名和准考证号从学信网获取成绩
     * @param  array   $data  用户信息
     * @return string  返回json格式的数据
     */	
	public function getScoreByNumber($data){
		$url='http://www.chsi.com.cn/cet/query';
		$preferer='http://www.chsi.com.cn/cet';
		$curl=new Curl();
		return $this->parse($curl->curl_get_chain($url,$data,$preferer));
	}

   /**
     * 解析学信网响应的html
     * @param  array   $data  用户信息
     * @return string  返回json格式的数据
     */		
	private function parse($data){
		$preg='/<table border="0" align="center" cellpadding="0" cellspacing="6" class="cetTable">(.*)<\/table>/Us';   //表格
		$preg1='/<td colspan="2">(.*)\s+<\/td>/Us'; //姓名、学校、级别、准考证
		$preg2='/<span class="colorRed">\s+(.*)\s+<\/span>/'; //总分
		$preg3='/<span class="color999">(.*)<\/span>\s+<\/th>\s+<td>\s+(.*)\s+<\/td>/';  
		//听力、阅读、写作和翻译
		
		if(preg_match($preg,$data,$data)){ 

			preg_match_all($preg1,$data[0],$data1); // 姓名、学校、级别、准考证
			preg_match($preg2,$data[0],$data2); // 总分
			preg_match_all($preg3,$data[0],$data3);  //听力、阅读、写作和翻译

			$score=array(
				'name'=>$data1[1][0],
				'school'=>$data1[1][1],
				'type'=>$data1[1][2],
				'number'=>$data1[1][3],
				'total'=>intval($data2[1]),
				'listen'=>intval($data3[2][0]),
				'read'=>intval($data3[2][1]),
				'writing'=>intval($data3[2][2])
			);

		   	return json_encode(array(
				"code"=>200,
				"message"=>"查询成功",
				"data"=>$score
			));
		}

		return json_encode(array(
				"code"=>400,
				"message"=>"查询失败，请检查你的信息无误"
		));  
	 
	}

	/**
	 * 添加用户信息
	 * @param 	array $data 用户信息
	 * @return  json
	 */
	public function addUserInfo($data){
		extract($data);

		$mysql=new Mysql($GLOBALS['config']['DB_HOST'],$GLOBALS['config']['DB_USER'],
			$GLOBALS['config']['DB_PASS'],$GLOBALS['config']['DB_NAME']);
		
		$res=$mysql->findOne("user","id"," name= '$xm' and number='$zkzh'");

		if($res){
		    return json_encode(array(
				"code"=>400,
				"message"=>"您已成功注册过,不能重复注册"
			));
		}else{
			$bool=$mysql->insert('user',array('name'=>$xm,'number'=>$zkzh,
				'email'=>$email,'time'=>time()
			));
			if($bool){
				return json_encode(array(
					"code"=>200,
					"message"=>"保存成功,四六级成绩出来后会发送成绩到您的邮箱"
				));
			}else{
				return json_encode(array(
					"code"=>500,
					"message"=>"服务器内部错误,保存失败"
				));	
			}
		}
	}
}












