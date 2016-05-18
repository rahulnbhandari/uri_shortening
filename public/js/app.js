
var app = angular.module('myApp', []);
app.controller('formCtrl', ["$scope","$http",function($scope,$http) {
    
    $scope.shortenedUrl = "";
     $scope.submitUrl = function() {
        var data = { 'url': $scope.newurl };
	
        $http.post("http://yik.bz/url", data ).then(function successCallback(response,$scope) {
          $scope.shortenedUrl = response.data    // this callback will be called asynchronously
    // when the response is available
  }, function errorCallback(response) {
      alert(response.error);
  })
};
}]);

/*
.success(function(response) {
}.fail(function(err) {
alert("xsxsx");
  errorMessage.name = "Some error message here."; 
}
    }
}]);
*/

