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
            iconUrl: './image/path-pin.png',
            iconSize: [28, 28]
        });


        function initMaps() {
            map = DG.map('map', {
                zoom: 13,
                center: [54.98, 82.89],
                fullscreenControl: false,
                zoomControl: false
            });
            DG.control.zoom({position: 'topright'}).addTo(map);
            map.locate({setView: true, maxZoom: 10});

            map.on('click', function (e) {
                console.log(e.latlng);
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
                    console.error(e.latlng);
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
    });

}

var MapDirective = function () {
    return {
        restrict: 'E',
        scope: {
            points: '=',
            addPoint: '&'
        },
        link: function (scope, el, attr) {
            function addLine(line) {
                DG.Wkt.geoJsonLayer(line).addTo(pathGroup);
                pathGroup.addTo(map);
                map.fitBounds(pathGroup.getBounds());
            }

            function addMarker(latLng) {
                DG.marker(latLng).addTo(markerGroup);
                markerGroup.addTo(map);
                map.fitBounds(markerGroup.getBounds());
            }

            scope.$watch('points', function () {
                if (pathGroup) {
                    pathGroup.removeFrom(map);
                    markerGroup.removeFrom(map);
                    for (i in scope.points.paths) {
                        addLine(scope.points.paths[i]);
                    }
                    for (i in scope.points.points) {
                        addMarker([scope.points.points[i].lat, scope.points.points[i].lon]);
                    }
                }


            })
        },
        controller: MapController,
        templateUrl: 'template/map-tpl.html'
    }
};