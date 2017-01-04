<?php

/*自动加载类*/
function __autoload($className){
	require __DIR__.'\\'.$className.'.class.php';
}

header("Access-Control-Allow-Origin:http://cet.lenshen.com"); //只允许本站提交数据,防ajax跨域 

/*判断是否为ajax请求,防止别人模拟post抓取数据*/
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"])&&strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){ 
    
    //有准考证查询
    if($_POST['has-number']==1){
    	$data=array('zkzh'=>$_POST['number'],'xm'=>$_POST['user']);
    	$cet= new Cet();
    	echo $cet->getScoreByHas($data);
    }
    //无准考证查询
    elseif($_POST['has-number']==0){
    	$data=array(
            'name'=>$_POST['user'],
            'province'=>$_POST['province'],
            'school'=>$_POST['school'],
            'type'=>$_POST['type']
        );
        $cet= new Cet();
        echo $cet->getScoreByNone($data);
    }
}else{ 
     echo "we caught you! you have no access!";
};





