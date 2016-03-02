MapController = ($scope, ListData)->
  $scope.height = pageHeight - 85;

  DG.then(()->
    markerGroup = DG.featureGroup();
    markerPathGroup = DG.featureGroup();
    pathGroup = DG.featureGroup();
    iconMarker = DG.icon({
      iconUrl: './image/pin-icon.png',
      iconSize: [30, 36],
      iconAnchor: [15, 26]
    });
    iconMarkerPath = DG.icon({
      iconUrl: './image/path-pin.png',
      iconSize: [28, 28]
    });


    initMaps = () ->
      map = DG.map('map', {
        zoom: 13,
        center: [54.98, 82.89],
        fullscreenControl: false,
        zoomControl: false
      });
      DG.control.zoom({position: 'topright'}).addTo(map);
      map.locate({setView: true, maxZoom: 10});

      map.on 'click', (e) ->
        popup = DG.popup()
        .setLatLng(e.latlng)
        .setContent('<div class="map-baloon"><input type="text" class="map-input"><button class="btn btn-ballon">ok</button></div>')
        .openOn(map);
        $('.btn-ballon').click ()->
          newItem = {
            after: 0,
            lat: e.latlng.lat,
            lon: e.latlng.lng,
            position: 0,
            title: $('.map-input').val(),
            type: "geo_point",
            latLon: e.latlng.lat
          };

          console.error(e.latlng)
          #$scope.listData.todo_list_items.push(newItem);
          $scope.addPoint({item: newItem})
          #console.log($scope);

          popup._closePopup();

    addMarker = (latLng)->
      DG.marker(latLng, {icon: iconMarker}).addTo(markerGroup);
      markerGroup.addTo(map);
      map.fitBounds(markerGroup.getBounds());


    addMarkerPath = (latLng) ->
      DG.marker(latLng, {icon: iconMarkerPath}).addTo(markerPathGroup);
      markerPathGroup.addTo(map);
      map.fitBounds(markerPathGroup.getBounds());

    outPath = (coordinates) ->
      color_path = ["#ffffff", "#ff4600"]
      weight_path = [12, 6];
      for i in [0..2]
        DG.polyline(coordinates, {color: color_path[i], weight: weight_path[i]}).addTo(pathGroup)

      pathGroup.addTo(map);
      map.fitBounds(pathGroup.getBounds());


    clearMap = () ->
      console.log("clear");
      pathGroup.removeFrom(map);
      markerGroup.removeFrom(map);
    initMaps()
  )