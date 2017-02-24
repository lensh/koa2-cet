var myCet = angular.module('myCet', []);

myCet.controller('mainController', function($scope, $http) {

	/*******************************查询成绩**********************************/
	$scope.btn={
		'disabled':false,
		'val':'查询'
	};

	//点击查询
	$scope.submitForm = function() {
		$scope.btn={
			'disabled':true,
			'val':'正在查询...'
		}
		$scope.data={};  //成绩结果
		$scope.info='';  //提示信息

		var req = 'user=' + $scope.user.name + '&number=' + $scope.user.number;

		$http({
			url: './model/query.php?action=1',
			method: "POST",
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			data: req
		}).success(function(data, header, config, status) {
			if(data.code==200){
				$scope.data=data.data;
				$scope.info=(data.data.total>=426)?'恭喜你通过本次CET考试，棒棒哒!':
				'革命尚未成功，同志仍需努力!';
				$scope.error=false;
			}else{
				$scope.error=true;
			}	

			$("#myModal").modal('show');

			$scope.btn={
				'disabled':false,
				'val':'查询'
			};

		}).error(function(data, header, config, status) {
			console.log(data);
		});
	}


	/***********************自动查询，用户填写信息*****************************/
	$scope.btn1={
		'disabled':false,
		'val':'保存信息'
	};

	//点击保存
	$scope.submitForm1 = function() {
		$scope.btn1={
			'disabled':true,
			'val':'正在保存...'
		}

		var req = 'user=' + $scope.user1.name + '&number=' + $scope.user1.number+
		'&email='+ $scope.user1.email;

		$http({
			url: './model/query.php?action=2',
			method: "POST",
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			data: req
		}).success(function(data, header, config, status) {
			alert(data.message);
			$scope.btn1={
				'disabled':false,
				'val':'保存信息'
			};

		}).error(function(data, header, config, status) {
			console.log(data);
		});
	}
	
});