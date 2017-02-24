<?php
#!/usr/bin/php -q  

//自动发邮件的入口
function __autoload($className){
	require_once '../model/'.$className.'.class.php';
}

set_time_limit(0);   //设置php的超时时间，即永不超时

$AutoCet=new AutoSendEmail();
$AutoCet->send();
