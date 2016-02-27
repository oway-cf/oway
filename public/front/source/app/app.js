var app = angular.module('oWay', ['ngResource']);

var ListModel = function ($resource, $location) {
    var path = 'http://' + $location.host();
    return $resource(path + '/api/list/:id', {id: '@id'},
        {
            get: {method: 'GET', isArray: false},
            create: {method: 'POST', isArray: false},
            update: {method: 'PUT', isArray: false},
        });
}

var SugestModel = function ($resource, $location) {
    var path = 'http://' + $location.host();

    return $resource(path + '/api/suggest/address/:query', {query: '@query'},
        {
            //smart: {method: 'GET', isArray: false},
            address: {method: 'GET', isArray: true},
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
        List.create({list: {title: 'sample', items: []}})
            .$promise
            .then(function (response) {
                $scope.list = response;
                //$scope.items = [];
                localStorage.setItem('listId', response.id)
            }, function () {
                $scope.list = {
                    id: 9,
                    title: "sample",
                    todo_list_items: [],
                };
            });
    } else {
        $scope.list = List.get({id: listId});
    }

    $scope.search = function () {
        Suggest.address({query: $scope.query})
            .$promise
            .then(function (response) {
                $scope.searchResult = response;
            });
    };

    $scope.pushItems = function () {
        List.update({
            key: $scope.list.id,
            list: $scope.list.todo_list_items
        });
    };

    $scope.addItem = function (item) {
        var listItem = {
            "key": "string",
            "title": item.title,
            "type": "geo_point",
            "position": $scope.list.todo_list_items.length,
            //"after": "string",
            //"before": "string",
            "lon": item.location.lot,
            "lat": item.location.lat,
        };

        $scope.list.todo_list_items.push(listItem);
        $scope.query = '';
        $scope.pushItems();
    }

    $scope.delItem = function (index) {
        $scope.list.todo_list_items.splice(index, 1);
        $scope.pushItems();
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
