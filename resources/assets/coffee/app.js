
var MapDirective = function () {
    return {
        restrict: 'E',
        scope: {
            points: '=',
            addPoint: '&'
        },
        link: function (scope, el, attr) {

        },
        controller: MapController,
        templateUrl: 'template/map-tpl.html'
    }
};