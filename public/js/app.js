
var app = angular.module('myApp', []);
app.controller('formCtrl', ["$scope","$http",function($scope,$http) {
    
     $scope.shortenedUrl = "";
     $scope.errorMessage = "";
     $scope.submitUrl = function() {
        var data = { 'url': $scope.newurl };
        $http.post("/url", data ).then(function successCallback(response) {
        $scope.errorMessage = "";
        $scope.shortenedUrl = response.data.url   // this callback will be called asynchronously
    // when the response is available
  }, function errorCallback(response) {
      $scope.shortenedUrl = "";
      $scope.errorMessage = response.data.error;
  })
};
}]);

