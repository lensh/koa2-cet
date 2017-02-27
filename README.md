# Cet

英语四六级成绩自动查询，你完全可以不用去学信网查询,当官方公布成绩时,系统会自动发送成绩到你的邮箱，
够快捷，够方便，同时提供免费接口API。

# 技术优势

* 自动查询，够快捷，够方便。在过去，四六级成绩公布后，我们需要去学信网查询成绩，如果准考证忘记
了的话，就要下载99宿舍来找回准考证号，过程实在是麻烦。而本系统只需要你考完试后填写相关信息，
待官网公布成绩后，就会在第一时间将你的成绩以邮件的形式通知你，省去很多不便。

* 系统足够安全。例如为了防止目录被偷窥，几乎在每个目录下都内置了index.html。另外也能防SQL注入、
XSS攻击等等。

* 提供接口API。本系统同时提供免费接口API，只需要调用接口即可查询成绩。

* 支持在线成绩查询。

* 性能很好。由于前台使用了AngularJS，极大地减少了DOM的操作。


# 原理解析

* 成绩的获取：使用CURL相关函数去学信网进行模拟请求，将抓取到的数据使用正则进行筛选分析。

* 自动查询：用户填写相关的信息，然后写到数据库里，并有标记字段status，0为还未发送邮件
(表明成绩还未公布)，然后使用linux的crontab定时任务工具，定时执行auto.shell脚本，去数据库里查找
status=0的记录，找到后再去学信网抓取成绩，最后将成绩以邮件的形式发给用户，同时将status置为1。


# 目录结构介绍
> dist   ----- 压缩后的css和js

>>  css		-----  压缩后的css目录

>>> styles.css      -----  压缩并合并后的css文件

>>> all.js      -----  压缩并合并后的js文件

>>  js		-----  压缩后的js目录  

> node_modules    ----- 与前端自动化构建工具gulp相关的node第三方模块

> src     -----  源文件目录  

>>  assets		-----  资源目录  

>>  bin		-----  与自动查询相关的bat或者shell文件的目录，使用时一定要留意

>>> auto.bat     -----  windows下实现自动查询的批处理文件，双击即可

>>> auto.php     -----  发送邮件的入口文件

>>> auto.shell     -----  linux下实现自动查询的shell脚本，需要结合crontab才能使用

>>  config		-----  数据库配置文件的目录

>>> config.php     -----  配置文件，包含了数据库的配置信息

>>  model		-----  后台业务逻辑处理目录

>>> AutoSendEmail.class.php     -----  发送邮件类

>>> Cet.class.php     -----  四六级成绩查询类

>>> Curl.class.php     -----  Curl操作类，模拟请求

>>> Mysql.class.php     -----  Mysql操作类

>>> query.php     -----  后台入口文件

>>  PHPMailer		-----  第三方邮件发送包目录

>>  index.html		-----  前台入口，网站首页

> cet.sql  	 -----  数据库导出文件 

> gulpfile.js  	 -----  自动化工具gulp的控制文件 

> package.json  	-----  包管理配置文件



# 使用说明

* 由于使用了__autoload自动加载类的方法，该方法为php新特性，因此需要php version>5.5的方可正常运行。如果不想使用这个方法,则把以下代码删除，并且使用require手动导入类文件。

```

 /*自动加载类*/
  function __autoload($className){
      require __DIR__.'\\'.$className.'.class.php';
  }

```

* 需要安装php_curl扩展并在php.ini里开启，具体怎样安装网上有很多教程。

* 数据库的信息配置在config目录下的config.php里，换成你的数据库的配置信息。

* bin目录下的bat、shell文件需要配置，文件里面写了配置的方法。

* 别忘了导入cet.sql文件到你的数据库里。



# API接口
* 本系统免费提供API接口，具体接口如下所示:

```
请求方式: POST
URL: http://cet.lenshen.com/src/model/query.php?action=1
POST数据格式：json
POST数据例子：{"name": "张三", "number": "360021162347654"}
请求成功返回json:
{ "code":200,
  "message":"查询成功",
  "data":{ "name":"张三", "school":"南昌大学", "type":"英语六级", "number":"360021162347654",
   "total":"530", "listen":"170", "read":"200", "writing":"160"
  }
}
请求失败返回json:
{ "code":400,
  "message":"查询失败，请检查你的信息是否无误"
}
```



# 具体效果：
* 首页

  ![](https://github.com/lensh/Cet/blob/master/src/assets/img/cet1.png)

* 查询结果

  ![](https://github.com/lensh/Cet/blob/master/src/assets/img/cet2.png)

* 邮件

  ![](https://github.com/lensh/Cet/blob/master/src/assets/img/cet3.png)

# FAQ
 官网:http://cet.lenshen.com
 若使用的过程中遇到问题，可以加官方群交流：611212696
