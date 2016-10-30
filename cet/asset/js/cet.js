
var Cet={
	'search':function(){
		$('#search1,#search2').click(function(){
			if($(this).get(0).id=='search1'){
				var user=$('input[name=user]').val(),
					number=$('input[name=number]').val();
				if(user==''||number=='') return;   //输入为空则直接返回
				$(this).val('正在查询,请稍等....').css('background','#ccc').get(0).disabled=true;
				var data='user='+user+'&number='+number+'&has-number=1';
				Cet.request(data);
			}else{
				var user=$('input[name=user1]').val(),
					province=$('input[name=province]').val(),
					school=$('input[name=school]').val(),
					type=$('input[name=type]').val();
				if(user==''||province==''||school==''||type=='') return;  //输入为空则直接返回
				$(this).val('正在查询,请稍等....').css('background','#ccc').get(0).disabled=true;
				var data={
					'user':user,
					'province':province,
					'school':school,
					'type':type=='四级'?1:2,
					'has-number':0
				}
				Cet.request(data);
			}
		});
	},
	'change':function(){
		$('#change').click(function(){
			if($(this).text()=='无准考证查询'){
				$(this).text('我有准考证');
				$('#form1').removeClass('show').addClass('hide').next().addClass('show');
			}else{
				$(this).text('无准考证查询');
				$('#form2').removeClass('show').addClass('hide').prev().addClass('show');	
			}
		});		
	},
	'modal':function(){
		$('#myModal').on('hide.bs.modal', function () { 
			$('#search1').val('查询').css('background','#49afcd').get(0).disabled=false;
			$('#search2').val('查询').css('background','#49afcd').get(0).disabled=false;
		});
	},
	'request':function(data){
		$.post('./lib/query.php',data,function(res){
			var result=JSON.parse(res);
			if(result.status=='200'){
				$('#r_xm').text(result.name);
				$('#r_listen').text(result.listen);
				$('#r_reading').text(result.read);
				$('#r_number').text(result.number);
				$('#r_time').text(result.time);
				$('#r_type').text(result.type);
				$('#r_writing').text(result.writing);
				$('#r_school').text(result.school);
				$('#r_total').text(result.total);
				if(result.total>=425) {
					$("#r_info").removeClass('alert-danger').addClass('alert-success').text('恭喜您通过本次CET考试!棒棒哒!');
				} 
				else {$("#r_info").removeClass('alert-success').addClass('alert-danger').text('革命尚未成功,同志仍需努力!');
				}		
				$('#error').hide().next().show();
			}
			else{
				$('#error').show().next().hide();
			}	
		    $("#myModal").modal('show');
		});
	},
	'count':function(){
	  $.get('./lib/count.php','',function(res){
		 var result=JSON.parse(res);
		 $("#times").text(result.times);
	  });
	  setInterval(function(){
		$.get('./lib/count.php','',function(res){
			var result=JSON.parse(res);
			$("#times").text(result.times);
		});
	  },5000);
	},
	'init':function(){
		$('input[name=user1]').focus();
		//禁止网页放入框架
		if(self != top){
			top.location.href=self.location.href;
		}
	},
	'run':function(){
		Cet.init();
		Cet.search();
		Cet.change();
		Cet.modal();
		Cet.count();	
	}
};
