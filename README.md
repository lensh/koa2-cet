# koa2-cet

基于Angular和Koa2的英语四六级成绩查询系统，提供免费API接口

# 预览

在线预览地址: https://cet.lenshen.com

# 技术栈

* **Angular**：实现前端页面构建
* **Koa2**：实现服务端具体业务逻辑
* **ES6**、**ES7**、**ES8**：服务端使用ES6语法，promise/async/await 处理异步
* **superagent**：爬虫的核心，进行模拟请求
* **cheerio**：解析DOM结构，爬取需要的数据
* **cors**：服务端返回数据时做了cors设置，允许跨域
* **jsonp**：支持JSONP请求，客户端需要传入回调函数名称
* **pm2**：服务端使用pm2部署，常驻进程，比forever好用得多（https://github.com/Unitech/pm2）
* **nginx**：服务端代理端口转发

# 使用说明

使用cnpm i 安装所有依赖，然后运行npm run dev，浏览器打开 http://localhost:8001

# API接口

本系统免费提供API接口，具体接口如下所示:
```
URL: https://cet.lenshen.com/api/search?user=姓名&number=准考证号
参数说明：
    user  姓名(需要先将中文进行urlencode编码)
    number  准考证号
请求方式: GET
请求成功返回json:
{ 
  "code":200,
  "message":"查询成功",
  "data":{  
  	"name":"成景文",   //姓名
  	"school":"山西大学",  //学校
  	"type":"英语四级",  //考试类别
	  "number":"140010171105929",  //准考证号
	  "total":"402",   //总分
	  "listen":"107",   //听力
	  "read":"153",  //阅读
	  "writing":"142"  //写作和翻译
  }
}
请求失败返回json:
{ 
  "code":400,
  "message":"查询失败，请检查你的信息是否无误"
}
注意：以上接口可以使用后台代理请求数据，也可以直接使用ajax/fetch/axios请求数据（因为设置了cors）



如果使用JSONP，则需要在url里传入callback：
URL:https://cet.lenshen.com/api/search?callback=cb&&number=准考证号&user=姓名
参数说明：  
    callback  回调函数名称
    user  姓名 
    number  准考证号
请求方式: GET
请求成功返回jsonp:
cb({ 
  "code":200,
  "message":"查询成功",
  "data":{  
    "name":"成景文",   //姓名
    "school":"山西大学",  //学校
    "type":"英语四级",  //考试类别
    "number":"140010171105929",  //准考证号
    "total":"402",   //总分
    "listen":"107",   //听力
    "read":"153",  //阅读
    "writing":"142"  //写作和翻译
  }
})
请求失败返回jsonp:
cb({ 
	"code":400,
	"message":"查询失败，请检查你的信息是否无误"
})
```

测试用户如下：

   姓名：成景文

   准考证号：140010171105929
   
# FAQ

若使用的过程中遇到问题，可以加官方群交流：611212696
