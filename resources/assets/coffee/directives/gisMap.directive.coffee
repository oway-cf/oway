MapDirective = ->
  link = (scope, el, attr)->
    console.log '123';
    addLine = (line) ->
      DG.Wkt.geoJsonLayer(line).addTo(pathGroup);
      pathGroup.addTo(map);
      map.fitBounds(pathGroup.getBounds());


    addMarker = (latLng) ->
      DG.marker(latLng).addTo(markerGroup);
      markerGroup.addTo(map);
      map.fitBounds(markerGroup.getBounds());


    scope.$watch 'points', () ->
      console.log 'points_watch', scope.points
      if (pathGroup)
        pathGroup.removeFrom(map);
        markerGroup.removeFrom(map);
        for i in scope.points.paths
          addLine(scope.points.paths[i])

        for i in scope.points.points
          addMarker [scope.points.points[i].lat, scope.points.points[i].lon]

  return {
  restrict: 'E'
  scope:
    points: '='
    addPoint: '&'
  link: link
  controller: MapController
  templateUrl: 'template/map-tpl.html'
  }
