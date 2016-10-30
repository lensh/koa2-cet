<?php

/*自动加载类*/
function __autoload($className){
	require __DIR__.'\\'.$className.'.class.php';
}

//判断是否为ajax请求
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
    echo "forbidden";
};





