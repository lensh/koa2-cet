var Cet = angular.module('myCet', []);

Cet.controller('mainController', function($scope, $http) {

	$scope.btn = {
		'disabled': false,
		'val': '查询'
	};
	$scope.year=new Date().getFullYear()

	$scope.submitForm = function() {
		$scope.btn = {
			'disabled': true,
			'val': '正在查询...'
		}
		$scope.data = {}; //成绩结果
		$scope.info = ''; //提示信息

		var req = 'user=' + $scope.user.name + '&number=' + $scope.user.number

		$http({
			url: '/api/search?' + req,
			method: "GET"
		}).success(function(data, header, config, status) {
			if (data.code == 200) {
				$scope.data = data.data;
				$scope.info = (data.data.total >= 426) ? '恭喜你通过本次CET考试，棒棒哒!' :
					'革命尚未成功，同志仍需努力!';
				$scope.error = false;
			} else {
				$scope.error = true;
			}

			$("#myModal").modal('show');

			$scope.btn = {
				'disabled': false,
				'val': '查询'
			};

		}).error(function(data, header, config, status) {
			console.log(data);
		});
	}

});