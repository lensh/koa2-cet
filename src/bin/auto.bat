::这里是系统自动查询四六级的关键所在，windows系统下使用该bat文件。双击即可自动查询.

:: 注意：
::	如果遇到Call to undefined function curl_init()的错误，请检查你的系统中是否已经安装
::  好了curl的相关扩展。
::  直接在url地址栏里访问auto.php可实现发送邮件的功能。

@echo off  
php auto.php  
pause  

:: 如有疑问请参考 :
:: http://blog.csdn.net/cangyingaoyou/article/details/20525669  
:: http://www.cnblogs.com/gis-user/p/5018073.html