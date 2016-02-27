var app = angular.module('oWay', ['ngResource']);
var pageWidth = document.documentElement.clientWidth,
    pageHeight = document.documentElement.clientHeight;

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
    $scope.height = pageHeight - 85;
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
                console.log(1);
            });
    }
}

function LeftFormDirective() {
    return {
        restrict: 'E',
        controller: LeftFormController,
        templateUrl: 'template/left-form.html'
    }
}

function MapController ($scope){
    $scope.width = pageWidth - 300;
    $scope.height = pageHeight - 85;

    DG.then(function() {
        var map,
            markerGroup = DG.featureGroup(),
            markerPathGroup = DG.featureGroup(),
            pathGroup = DG.featureGroup();
        var iconMarker = DG.icon({
            iconUrl: './image/pin-icon.png',
            iconSize: [30, 36],
            iconAnchor: [15, 26]
        });
        var iconMarkerPath = DG.icon({
            iconUrl: './image/path-pin.png',
            iconSize: [28, 28]
        });
        var iconStartMarkerPath = DG.icon({
            iconUrl: './image/start-pin.png',
            iconSize: [28, 28]
        });


        function initMaps (){
            map = DG.map('map', {
                zoom: 13,
                center: [54.98, 82.89],
                fullscreenControl: false
            });

            map.locate({setView: true, maxZoom: 10});
        }

        function addMarker (latLng){
            DG.marker(latLng,{icon: iconMarker}).addTo(markerGroup);
            markerGroup.addTo(map);
            map.fitBounds(markerGroup.getBounds());
        }

        function addMarkerPath (latLng){
            DG.marker(latLng,{icon: iconMarkerPath}).addTo(markerPathGroup);
            markerPathGroup.addTo(map);
            map.fitBounds(markerPathGroup.getBounds());
        }

        function outPath (coordinates) {
            var color_path = ["#ffffff", "#ff4600"],
                weight_path = [12, 6];
            for (var i = 0; i < 2; i++) {
                DG.polyline(coordinates, {
                    color: color_path[i],
                    weight: weight_path[i]
                }).addTo(pathGroup);
            }
            pathGroup.addTo(map);
            map.fitBounds(pathGroup.getBounds());
        }

        function clearMap (){
            console.log("clear");
            pathGroup.removeFrom(map);
            markerGroup.removeFrom(map);
        }

        initMaps();
        addMarker([54.98, 83.09]);

        addMarkerPath([54.98, 82.89]);
        addMarkerPath([55.069288, 82.816615]);
        addMarkerPath([55.011648, 82.902103]);
        addMarkerPath([54.944714, 82.903152]);
        addMarkerPath([54.928935, 82.850967]);


        outPath([[54.98, 82.89], [55.069288, 82.816615], [55.011648, 82.902103], [54.944714, 82.903152], [54.928935, 82.850967]]);

        document.getElementById('clearMap').addEventListener('click', function () {
            clearMap() ;
        });
    });

}
