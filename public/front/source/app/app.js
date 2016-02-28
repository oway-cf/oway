var app = angular.module('oWay', ['ngResource', 'ui.sortable']);
pageHeight = document.documentElement.clientHeight;
var map,
    markerGroup,
    pathGroup;

var ListData = function () {
    return {
        ways: [],
        coords: [],
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
    $scope.coords = ListData.coords;
    console.info(ListData);
    $scope.height = pageHeight - 85;
    $scope.heightList = $scope.height - 60;
}

function LeftFormController($scope, List, Suggest, ListData, $location) {

    $scope.height = pageHeight - 85;
    $scope.coords = ListData.coords;
    listId = $location.path().length == 0 ? localStorage.getItem('listId') : $location.path().substr(1);

    $scope.query = '';
    if (!listId || Number.isInteger(listId)) {
        List.create({list: {title: 'sample', items: []}})
            .$promise
            .then(function (response) {
                $scope.list = response;
                $scope.coords = $scope.list.todo_list_items;
                console.info(ListData);
                //$scope.items = [];
                localStorage.setItem('listId', response.id)
            }, function () {
                $scope.list = {
                    id: 9,
                    title: "sample",
                    items: [],
                };
            });
    } else {
        $scope.list = List.get({id: listId});
        $scope.coords = $scope.list.todo_list_items;
        console.info(ListData);
        localStorage.setItem('listId', listId);
    }

    $scope.search = function () {
        Suggest.address({query: $scope.query})
            .$promise
            .then(function (response) {
                $scope.searchResult = response;
            });
    };

    $scope.sortableOptions = {
        update: function (e, ui) {
            $scope.pushItems();
        },
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
        $scope.coords = $scope.list.todo_list_items;

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
                $scope.coords = response.points;
            }, function (response) {
                $scope.wayBuilding = false;
                alert('Ошибка расчета маршрута');
            })
    }
    $scope.addItem = function (item) {
        //console.log(item);
        var listItem = {
                "key": "string",
                "title": item.title,
                "type": "geo_point",
                "position": 0,
                "after": 0,
                //"before": "string",
                "lon": item.location ? item.location.lon : item.lon,
                "lat": item.location ? item.location.lat : item.lat,

            }
            ;
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

function MapController($scope, ListData) {
    $scope.height = pageHeight - 85;

    DG.then(function () {
        markerGroup = DG.featureGroup();
        markerPathGroup = DG.featureGroup();
        pathGroup = DG.featureGroup();
        var iconMarker = DG.icon({
            iconUrl: './image/pin-icon.png',
            iconSize: [30, 36],
            iconAnchor: [15, 26]
        });
        var iconMarkerPath = DG.icon({
            iconUrl: './image/pin-way.png',
            iconSize: [28, 28]
        });
        var iconMarkerPathStart = DG.icon({
            iconUrl: './image/start-pin.png',
            iconSize: [28, 28]
        });
        var iconMarkerPathFinish = DG.icon({
            iconUrl: './image/finish-pin.png',
            iconSize: [28, 28]
        });


        function initMaps() {
            map = DG.map('map', {
                zoom: 10,
                center: [54.98, 82.89],
                fullscreenControl: false,
                zoomControl: false,
                minZoom: 10,
                maxZoom: 20
            });
            DG.control.zoom({position: 'topright'}).addTo(map);
            map.locate({setView: true, maxZoom: 10});

            map.on('click', function (e) {
                console.dir(e);
                var popup = DG.popup()
                    .setLatLng(e.latlng)
                    .setContent('<div class="map-baloon"><input type="text" class="map-input"><button class="btn btn-ballon">ok</button></div>')
                    .openOn(map);
                $('.btn-ballon').click(function () {

                    newItem = {
                        after: 0,
                        lat: e.latlng.lat,
                        lon: e.latlng.lng,
                        position: 0,
                        title: $('.map-input').val(),
                        type: "geo_point",
                        latLon: e.latlng.lat
                    };
                    //$scope.listData.todo_list_items.push(newItem);
                    $scope.addPoint({item: newItem});
                    //console.log($scope);

                    popup._closePopup();
                });
            });
        }

        function addMarker(latLng) {
            DG.marker(latLng, {icon: iconMarker}).addTo(markerGroup);
            markerGroup.addTo(map);
            map.fitBounds(markerGroup.getBounds());
        }

        function addMarkerPath(latLng) {
            DG.marker(latLng, {icon: iconMarkerPath}).addTo(markerPathGroup);
            markerPathGroup.addTo(map);
            map.fitBounds(markerPathGroup.getBounds());
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
        //addMarker([54.98, 83.09]);
        //
        //addMarkerPath([54.98, 82.89]);
        //addMarkerPath([55.069288, 82.816615]);
        //addMarkerPath([55.011648, 82.902103]);
        //addMarkerPath([54.944714, 82.903152]);
        //addMarkerPath([54.928935, 82.850967]);
        //
        //
        //outPath([[54.98, 82.89], [55.069288, 82.816615], [55.011648, 82.902103], [54.944714, 82.903152], [54.928935, 82.850967]]);

        //document.getElementById('clearMap').addEventListener('click', function () {
        //    clearMap() ;
        //});
    });

}

var MapDirective = function () {
    return {
        restrict: 'E',
        //replace: true,
        scope: {
            points: '=',
            addPoint: '&',
            coords: '='
        },
        link: function (scope, el, attr) {

            function addLine(line) {
                DG.Wkt.geoJsonLayer(line).addTo(pathGroup);
                pathGroup.addTo(map);
                map.fitBounds(pathGroup.getBounds());
            }

            function addMarker(latLng, title) {
                if (!markerGroup) {
                    return console.warn('markerGroup undefinde');
                }
                var iconMarker = DG.icon({
                    iconUrl: './image/pin-icon.png',
                    iconSize: [30, 36],
                    iconAnchor: [15, 26]
                });

                DG.marker(latLng, {icon: iconMarker})
                    .addTo(markerGroup)
                    .bindPopup(title || 'default title');
                markerGroup.addTo(map);
                map.fitBounds(markerGroup.getBounds());
            }

            function clear() {
                if (pathGroup) {
                    pathGroup.clearLayers();
                    markerGroup.clearLayers();
                }
            }

            function drawBalloons(coords) {
                for (i in coords) {
                    //add
                }
            }

            scope.$watch('coords', function () {
                console.log('watch coords', scope.coords);
                clear();
                for (var i in scope.coords) {
                    addMarker(
                        [scope.coords[i].lat, scope.coords[i].lon],
                        scope.coords[i].title
                    );
                }
            });
            scope.$watch('points', function () {
                if (pathGroup) {
                    clear();
                    for (i in scope.points.paths) {
                        addLine(scope.points.paths[i]);
                    }
                    for (i in scope.points.points) {
                        addMarker(
                            [scope.points.points[i].lat, scope.points.points[i].lon],
                            scope.points.points[i].title
                        );
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