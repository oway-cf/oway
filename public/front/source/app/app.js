var app = angular.module('oWay', ['ngResource']);
var map,
    markerGroup,
    pathGroup;


var ListData = function () {
    return {
        ways: []
    };
}


var ListModel = function ($resource, $location) {
    var path = 'http://' + $location.host();
    return $resource(path + '/api/list/:id/:type', {id: '@id', type: '@type'},
        {
            get: {method: 'GET', isArray: false},
            create: {method: 'POST', isArray: false},
            up: {method: 'POST', isArray: false, params: {type: 'update'}},
            way: {method: 'GET', isArray: false, params: {type: 'way'}},
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

var MainController = function ($scope, ListData, List) {
    $scope.ways = ListData.ways;
}


function LeftFormController($scope, List, Suggest, ListData) {
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
        $data = ({
            id: $scope.list.id,
            key: $scope.list.id,
            list: {
                title: 'abrvalg',
                items: $scope.list.todo_list_items
            }
        });
        List.up($data);
    };

    $scope.calcRoute = function () {
        $scope.wayBuilding = true;
        List.way({
            id: $scope.list.id
        }).$promise
            .then(function (response) {
                $scope.wayBuilding = false;
                ListData.ways = response;
                $scope.ways = response;
            })
    }
    $scope.addItem = function (item) {
        var listItem = {
            "key": "string",
            "title": item.title,
            "type": "geo_point",
            "position": $scope.list.todo_list_items.length,
            "after": 0,
            //"before": "string",
            "lon": item.location.lon,
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

function LeftFormDirective() {
    return {
        restrict: 'E',
        controller: LeftFormController,
        templateUrl: 'template/left-form.html'
    }
}

function MapController($scope) {

    DG.then(function () {
        markerGroup = DG.featureGroup();
        pathGroup = DG.featureGroup();

        function initMaps() {
            map = DG.map('map', {
                zoom: 13,
                center: [54.98, 82.89],
                fullscreenControl: false
            });

            map.locate({setView: true, maxZoom: 10});
        }

        function addMarker(latLng) {
            DG.marker(latLng).addTo(markerGroup);
            markerGroup.addTo(map);
            map.fitBounds(markerGroup.getBounds());
        }

        function outPath(coordinates) {
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

        function clearMap() {
            console.log("clear");
            pathGroup.removeFrom(map);
            markerGroup.removeFrom(map);
        }

        initMaps();

        //addMarker([54.98, 82.89]);
        //addMarker([55.069288, 82.816615]);
        //addMarker([55.011648, 82.902103]);
        //addMarker([54.928935, 82.850967]);
        //outPath([[54.98, 82.89], [55.069288, 82.816615], [55.011648, 82.902103], [54.944714, 82.903152], [54.928935, 82.850967]]);

        document.getElementById('clearMap').addEventListener('click', function () {
            clearMap();
        });
    });

}


var MapDirective = function () {
    return {
        restrict: 'E',
        //replace: true,
        scope: {
            points: '='
        },
        link: function (scope, el, attr) {
            function addLine(line) {
                DG.Wkt.geoJsonLayer(line).addTo(pathGroup);
                pathGroup.addTo(map);
                map.fitBounds(pathGroup.getBounds());
            }

            scope.$watch('points', function () {
                if (pathGroup) {
                    pathGroup.removeFrom(map);
                    markerGroup.removeFrom(map);
                    for(i in scope.points.path)
                    {
                        addLine(scope.points.path[i]);
                    }
                }


            })
        },
        controller: MapController,
        templateUrl: 'template/map-tpl.html'
    }
};


app.controller('mapCtrl', MapController);
app.controller('leftFormCtrl', LeftFormController);
app.controller('MainController', MainController);
app.directive('leftForm', LeftFormDirective);
app.directive('gisMap', MapDirective);
app.factory('List', ListModel);
app.factory('Suggest', SugestModel);
app.factory('ListData', ListData);