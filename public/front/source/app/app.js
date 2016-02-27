require ('angular');
require ('angular-route');
require ('../libs/dg-maps.js');

var app = angular.module('oWay', ['ngRoute']);

app.controller('leftFormCtrl', LeftFormController);
app.directive('leftForm', LeftFormDirective);


function LeftFormController ($scope){
    $scope.addMarker = addMarker([54.98, 82.89]);
}

function LeftFormDirective (){
    return {
        restrict: 'E',
        controller: 'leftFormCtrl',
        templateUrl: 'template/left-form.html'
    }
}