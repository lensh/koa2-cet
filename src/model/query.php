<?php
//后台入口文件

function __autoload($className){
	require __DIR__.'\\'.$className.'.class.php';
}


//如果有提交信息
if(!empty($_POST)){

	//导入配置文件里的信息
	$GLOBALS['config']=require_once('../config/config.php');

	//安全过滤
	$_POST=array_map('addslashes',$_POST);  // 防sql注入
	$_POST=array_map('mysql_real_escape_string',$_POST);   //防xss攻击

	if(empty($_GET['action'])) return;

	//查询成绩
	if(intval($_GET['action']==1)){   
		
		$data=array(
			'zkzh'=>$_POST['number'],
			'xm'=>$_POST['user']
		);

		$cet= new Cet();
		echo $cet->getScoreByNumber($data);
	}

	//新增用户
	else if(intval($_GET['action']==2)){  

		$data=array(
			'zkzh'=>$_POST['number'],
			'xm'=>$_POST['user'],
			'email'=>$_POST['email']
		);

		$cet= new Cet();
		echo $cet->addUserInfo($data);
	}
}

