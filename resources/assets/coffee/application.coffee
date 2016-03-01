pageHeight = document.documentElement.clientHeight
map = markerGroup = pathGroup = null

app = angular.module 'oWay', ['ngResource', 'ui.sortable']

app
.controller 'mapCtrl', MapController
.controller 'leftFormCtrl', LeftFormController
.controller 'MainController', MainController
.directive 'leftForm', LeftFormDirective
.directive 'gisMap', MapDirective
.factory 'List', ListModel
.factory 'Suggest', SugestModel
.factory 'ListData', ListData 