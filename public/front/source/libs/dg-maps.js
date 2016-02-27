
var map;

function initMaps (){
    map = DG.map('map', {
        zoom: 13,
        center: [54.98, 82.89],
        fullscreenControl: false
    });

    map.locate({setView: true, maxZoom: 10});
}

function addMarker (latLng){
    DG.marker(latLng).addTo(map);
}

DG.then(function() {
    initMaps();
});