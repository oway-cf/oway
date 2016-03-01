MapDirective = ->
  link = ->

  restrict: 'e'
  scope:
    points: '='
    addPoint: '&'
  link: link
  controller: MapController
  templateUrl: 'template/map-tpl.html'
