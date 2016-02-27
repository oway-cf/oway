var app = angular.module('oWay', ['ngResource']);

var ListModel = function ($resource, $location) {
    var path = $location.host() == 'localhost' ? 'http://hackathon/' : 'http://oway.cf/';
    return $resource(path + 'api/list/:id', {id: '@id'},
        {
            get: {method: 'GET', isArray: false},
            create: {method: 'POST', isArray: false},
            update: {method: 'PUT', isArray: false},
        });
}

var SugestModel = function ($resource, $location) {
    var path = $location.host() == 'localhost' ? 'http://hackathon/' : 'http://oway.cf/';
    return $resource(path + 'api/suggest/address/?query=:query', {query: '@query'},
        {
            smart: {method: 'GET', isArray: false},
            address: {method: 'GET', isArray: false},
        });
}

app.controller('mapCtrl', MapController);
app.controller('leftFormCtrl', LeftFormController);
app.directive('leftForm', LeftFormDirective);
app.factory('List', ListModel);
app.factory('Suggest', SugestModel);

function LeftFormController($scope, List, Suggest) {
    listId = localStorage.getItem('listId');
    $scope.query = '';
    if (!listId) {
        List.create()
            .$promise
            .then(function (response) {
                $scope.list = response.key;
                //$scope.items = [];
                localStorage.setItem('listId', response.key)
            });
    } else {
        $scope.list = List.get({id: listId});
    }

    $scope.search = function () {
        $scope.searchResult = Suggest.address({query: $scope.query});
    }
}

function MapController($scope) {
    var map;

    function initMaps() {
        map = DG.map('map', {
            zoom: 13,
            center: [54.98, 82.89],
            fullscreenControl: false
        });

        map.locate({setView: true, maxZoom: 10});
    }

    function addMarker(latLng) {
        DG.marker(latLng).addTo(map);
    }

    DG.then(function () {
        initMaps();
    });

}

function LeftFormDirective() {
    return {
        restrict: 'E',
        controller: LeftFormController,
        templateUrl: 'template/left-form.html'
    }
}
