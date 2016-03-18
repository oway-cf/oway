MainController = ($scope, ListData, List) ->
  $scope.ways = ListData.ways;
  $scope.height = pageHeight - 85;
  $scope.heightList = $scope.height - 60;
