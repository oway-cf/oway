var app = angular.module('oWay', []);

app.controller('mapCtrl', MapController);
app.controller('leftFormCtrl', LeftFormController);
app.directive('leftForm', LeftFormDirective);

function LeftFormController ($scope){

}

function LeftFormDirective (){
    return {
        restrict: 'E',
        controller: 'leftFormCtrl',
        templateUrl: 'template/left-form.html'
    }
}

function MapController ($scope){
    DG.then(function() {
        var map,
            markerGroup = DG.featureGroup(),
            pathGroup = DG.featureGroup();

        function initMaps (){
            map = DG.map('map', {
                zoom: 13,
                center: [54.98, 82.89],
                fullscreenControl: false
            });

            map.locate({setView: true, maxZoom: 10});
        }

        function addMarker (latLng){
            DG.marker(latLng).addTo(markerGroup);
            markerGroup.addTo(map);
            map.fitBounds(markerGroup.getBounds());
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
        addMarker([54.98, 82.89]);
        addMarker([55.069288, 82.816615]);
        addMarker([55.011648, 82.902103]);
        addMarker([54.928935, 82.850967]);
        outPath([[54.98, 82.89], [55.069288, 82.816615], [55.011648, 82.902103], [54.944714, 82.903152], [54.928935, 82.850967]]);

        document.getElementById('clearMap').addEventListener('click', function () {
            clearMap() ;
        });
    });

}