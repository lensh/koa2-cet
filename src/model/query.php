<?php
//后台入口文件

//使用__autoload放到服务器上可能会报错，我们还是建议使用require导入
require './AutoSendEmail.class.php';
require './Cet.class.php';
require './Curl.class.php';
//如果有提交信息
if(!empty($_POST)){

	//导入配置文件里的信息
	$GLOBALS['config']=require_once('../config/config.php');

	//先进行安全过滤
	$_POST=array_map('addslashes',$_POST);  // 防sql注入
	//$_POST=array_map('mysql_real_escape_string',$_POST); //防xss攻击，如果服务器不支持mysql，则这句应删除

	if(empty($_GET['action'])) return;

	//如果是查询成绩
	if(intval($_GET['action']==1)){   
		
		$data=array(
			'zkzh'=>$_POST['number'],
			'xm'=>$_POST['user']
		);

		$cet= new Cet();
		echo $cet->getScoreByNumber($data);
	}

	//如果是新增用户
	else if(intval($_GET['action']==2)){  

		$data=array(
			'zkzh'=>$_POST['number'],
			'xm'=>$_POST['user'],
			'email'=>$_POST['email']
		);

		$cet= new Cet();
		$info=$cet->addUserInfo($data);
		echo $info;
		$arr=json_decode($info,true);
		if($arr['code']==200){
			$AutoCet=new AutoSendEmail();
	    	$AutoCet->sendOne($arr['data'][0]);		
		}
	}
}

