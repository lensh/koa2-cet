# cet
英语四六级有准考证查询
# 核心技术
使用php代理的方式实现四六级成绩的查询，其中php使用了curl相关操作去学信网抓取数据。然后前台使用jq post来获取后台返回的数据，并将数据动态写回到静态页面里。

# 技术优势
1.无准考证查询和有准考证查询都在一个页面里，查询操作用了ajax，查询结果以淡入淡出的模态框的形式展现。整个查询流程无任何页面跳转，极大地提高了用户的体验度。

2.使用了memcached缓存技术，如果用户已经查找过信息，则直接从缓存里获取数据，无需访问数据库，极大地缓解了数据库的压力，而且提高了查询速度。
```php
    //获取
    $number=$data['zkzh'];
    $memcached=new Memcached();
    if($memcached->get($number)){
	$result=json_encode($memcached->get($number));
	$memcached->close();
	return $result;
    }
    
    //设置
    $memcached=new Memcached();
    $memcached->set($array['number'],$array);   //写入缓存
    $memcached->close();
```

3.前台使用了ajax技术及定时器来获取查询过信息的用户的数量。由于数据量很小，所以优先选择了文件存储的方式，而不是数据库，极大地节约了资源。

```php
   /**
     * 统计查询的次数，并写入文件
     * @return void
     */	
	private function writeCount(){
		$times=file_get_contents('../info/count.txt');
		file_put_contents('../info/count.txt',intval($times)+1);
	}
  
  //count.php的内容
  /*在首页显示查询的次数*/
  $times=file_get_contents('../info/count.txt');
  $arr=array('times'=>$times);
  echo json_encode($arr);

```

4.前台使用了bootstrap框架，兼容PC，IPad和移动端，适配度高。

5.服务器设置了请求来源仅限于本域名，以及只有在ajax请求的情况下才能处理业务逻辑，有效地防止了别人的模拟请求。

```php
  header("Access-Control-Allow-Origin:http://cet.lenshen.com"); //只允许本站提交数据,防ajax跨域 
  
  //只能是ajax请求，以防止别人利用curl的post抓取数据
  if(isset($_SERVER["HTTP_X_REQUESTED_WITH"])&&strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest")
```
6.能有效地防止本站放入框架里。

```js
  //禁止网页放入框架
  if(self != top){
	top.location.href=self.location.href;
  }
```
# 项目目录结构介绍
asset目录下放了资源文件，比如css，js，图片等等。

info目录下存放了用来统计查询过信息的用户数量的文件。

lib目录下放的是整个查询流程处理业务逻辑的后台文件。各个文件的介绍如下：

  Curl.class.php  ---- 进行curl操作的核心类，对post请求和get请求的方法对外开放，外部只需要直接调用该方法即可。
  
  Memcached.class.php   ----- 进行缓存操作的核心类，对set和get方法对外开放。
  
  Cet.class.php         ----- 进行四六级查询的核心类
  
  count.php             ----- 负责后台统计查询次数，返回json数据
  
  query.php             ------接收前台用户填写的表单信息，然后调用Cet类的方法，返回json数据
  
  
index.html    -------项目入口文件，纯静态
  
# 使用说明

1.由于使用了__autoload自动加载类的方法，该方法为php新特性，因此需要php version>5.5的方可正常运行。如果不想使用这个方法,则把以下代码删除，并且使用require手动导入类文件。
  ```php
  /*自动加载类*/
  function __autoload($className){
	  require __DIR__.'\\'.$className.'.class.php';
  }
  ```

2.必须得安装并开启curl扩展，并且安装相关的依赖。具体怎样安装网上有很多教程。

3.必须得安装并开启php-memcache扩展，并安装memcached服务。具体怎样安装网上也有很多教程。

# 测试地址

 目前该项目已上线，可通过 http://cet.lenshen.com 测试。
 效果如下：
  首页
  ![](https://github.com/lensh/cet/blob/master/cet/asset/image/1.png)
  查询结果
  ![](https://github.com/lensh/cet/blob/master/cet/asset/image/2.png)
  
# FAQ
  如果在使用的过程中遇到问题，可以加我QQ：986992484。
