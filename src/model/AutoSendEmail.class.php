<?php
/**
 * 自动发送四六级成绩邮件类
 * @author leshen <986992484@qq.com>
 * @version 1.1
 */
class AutoSendEmail {

	private $mysql;

	/**
	 * 构造方法，连接mysql
	 */
	public function __construct(){
		$this->mysql=new Mysql($GLOBALS['config']['DB_HOST'],$GLOBALS['config']['DB_USER'],
			$GLOBALS['config']['DB_PASS'],$GLOBALS['config']['DB_NAME']);
	}

	/**
	 * 发送邮件
	 * @return 
	 */
	private function sendEmail($to, $title, $content){

		require_once('../PHPMailer/class.phpmailer.php');
	    $mail = new PHPMailer();
	    // 设置为要发邮件
	    $mail->IsSMTP();
	    // 是否允许发送HTML代码做为邮件的内容
	    $mail->IsHTML(TRUE);
	    // 是否需要身份验证
	    $mail->SMTPAuth=TRUE;
	    $mail->CharSet='UTF-8';
	    $mail->From='m18296764976_1@163.com';
	    $mail->FromName='AutoCet官方';
	    $mail->Host='smtp.163.com';
	    $mail->Username='m18296764976_1';
	    $mail->Password='kiss12345';
	    // 发邮件端口号默认25
	    $mail->Port = 25;
	    // 收件人
	    $mail->AddAddress($to);
	    // 邮件标题
	    $mail->Subject=$title;
	    // 邮件内容
	    $mail->Body=$content;
	    return($mail->Send());
	}

	/**
	 * 发送邮件
	 * @return void
	 */
	public function send(){
		$data=$this->getData();
		if(count($data)==0) return;  //如果没有status=0的记录，则直接返回
		$cet= new Cet();
		foreach ($data as $k => $v) {		
			$res=$cet->getScoreByNumber(array('zkzh'=>$v['number'],'xm'=>$v['name']));
			$arr=json_decode($res,1);

			if($arr['code']==400) continue;  //如果没有查询到成绩，则不发送邮件
			extract($arr['data']);
			
			$content =<<<HTML
			<p style="font-size: 18px;font-family: '微软雅黑';">
			四六级官网已公布成绩,您的CET成绩如下:</p><br/>
			<p style="font-family: '微软雅黑';line-height: 25px">姓名:
				<span style="color:red;font-weight: bolder;">$name</span>
			</p> 
			<p style="font-family: '微软雅黑';line-height: 25px">学校:<span style="color: green;padding-left: 5px">$school</span></p>
			<p style="font-family: '微软雅黑';line-height: 25px">考试类别:<span style="color: green;padding-left: 5px">$type</span></p>  
			<p style="font-family: '微软雅黑';line-height: 25px">准考证号:<span style="color: green;padding-left: 5px">$number</span></p>                 
			<p style="font-family: '微软雅黑';line-height: 25px">总分:<span style="color: green;padding-left: 5px">$total</span></p>    
			<p style="font-family: '微软雅黑';line-height: 25px">听力:<span style="color: green;padding-left: 5px">$listen</span></p>  
			<p style="font-family: '微软雅黑';line-height: 25px">阅读:<span style="color: green;padding-left: 5px">$read</span></p>  
			<p style="font-family: '微软雅黑';line-height: 25px">写作和翻译:<span style="color: green;padding-left: 5px">$writing</span></p>
HTML;
			//发送邮件
	  		$this->sendEmail($v['email'], '四六级成绩结果通知',$content);

	  		//更新状态
	  		$this->update($v['id']);
		}

	}

	/**
	 * 取出还未发送邮件的记录
	 * @return array
	 */
	private function getData(){
 		$data=$this->mysql->select('user','*','status=0');
 		return $data;
	}

	/**
	 * 取出后更新记录的状态
	 * @param  $id  记录的id
	 * @return void
	 */
	private function update($id){
		$bool=$this->mysql->update('user',array('status'=>1),$id);
	}
}