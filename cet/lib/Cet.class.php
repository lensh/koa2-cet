<?php

/**
 *  查询四六级成绩类
 *  @author leshen <986992484@qq.com>
 *  @version 1.1
 */

class Cet{

   /**
     * 有准考证获取成绩,从学信网抓取
     * @param  array   $data  用户信息
     * @return string  返回json格式的数据
     */	
	public function getScoreByHas($data){
		//先判断缓存里有没有数据，有则直接获取缓存的数据，没有则查询
		$number=$data['zkzh'];
		$memcached=new Memcached();
		if($memcached->get($number)){
			$result=json_encode($memcached->get($number));
			$memcached->close();
			return $result;
		}
		$url='http://www.chsi.com.cn/cet/query';
		$preferer='http://www.chsi.com.cn/cet';
		$curl=new Curl();
		return $this->parse($curl->curl_get_chain($url,$data,$preferer));
	}

   /**
     * 解析学信网响应的html，对有准考证查询有效
     * @param  array   $data  用户信息
     * @return string  返回json格式的数据
     */		
	private function parse($data){
	    $table_preg='/<table border="0" align="center" cellpadding="0" cellspacing="6" class="cetTable">.+<\/table>/Us';
	    $tr_preg='/<tr>\s+<th>(.*)<\/th>\s+<td>(.*)<\/td>\s+<\/tr>/Us';
	    $total_preg='/<span class="colorRed">\s+(.*)\s+<\/span>/Us';
	    $detail_preg='/<span class="color666">(.*)<\/span>\s+(.+)\s+<br \/>/Us';
	    $write_preg='/<span class="color666">写作与翻译：<\/span>\s+(.+)\s+<\/td>/Us';
	    if(preg_match($table_preg,$data)){
			preg_match($table_preg,$data,$table);
			preg_match_all($tr_preg,$table[0],$trs);  //考试信息
			preg_match($total_preg,$table[0],$total);  //总分
			preg_match_all($detail_preg,$table[0],$detail);  //每项的分数
			preg_match($write_preg,$table[0],$write);  //写作与翻译的分单独匹配
			
			$array=array(
				'status'=>200,
				'name'=>$trs[2][0],
				'school'=>$trs[2][1],
				'type'=>$trs[2][2],
				'number'=>$trs[2][3],
				'time'=>$trs[2][4],
				'total'=>intval($total[1]),
				'listen'=>intval($detail[2][0]),
				'read'=>intval($detail[2][1]),
				'writing'=>intval($write[1])
			);
			$this->writeCount();
			$memcached=new Memcached();
			$memcached->set($array['number'],$array);   //写入缓存
			$memcached->close();
			return json_encode($array);
		}
		return json_encode(array('status'=>501));
	}

   /**
     * 无准考证获取成绩
     * @param  array   $data  用户信息
     * @return string  返回json格式的数据
     */	
	public function getScoreByNone($data){
		//先判断缓存里有没有数据，有则直接获取缓存的数据，没有则查询
		$name=$data['name'];
		$memcached=new Memcached();
		if($memcached->get($name)){
			$result=json_encode($memcached->get($name));
			$memcached->close();
			return $result;
		}	
		$url='http://cet.zy62.com/query/2';
		$curl=new Curl();
		$result=$curl->curl_post($url,$data);
		$result_obj=json_decode($result);
		if($result_obj->status==200){
			//获取成绩
			$result_arr=array(
				'status'=>200,
				'name'=>$result_obj->result->name,
				'number'=>$result_obj->result->num,
				'school'=>$result_obj->result->school,
				'type'=>$result_obj->result->type,
				'time'=>$result_obj->result->time,
				'total'=>$result_obj->score->totleScore,
				'listen'=>$result_obj->score->tlScore,
				'read'=>$result_obj->score->ydScore,
				'writing'=>$result_obj->score->xzpyScore,
			);
			$this->writeCount();
		        $memcached=new Memcached();
			$memcached->set($name,$result_arr);   //写入缓存
			$memcached->close();
			return json_encode($result_arr);
		}
	    return $result;
	}

   /**
     * 统计查询的次数，并写入文件
     * @return void
     */	
	private function writeCount(){
		$times=file_get_contents('../info/count.txt');
		file_put_contents('../info/count.txt',intval($times)+1);
	}
}












