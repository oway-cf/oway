LeftFormController = ($scope, List, Suggest, ListData)->
  $scope.height = pageHeight - 85;
  listId = localStorage.getItem('listId');
  $scope.query = '';
  if (!listId)
    List.create({list: {title: 'sample', items: []}})
    .$promise
    .then((response)->
      $scope.list = response
      $scope.items = [];
      localStorage.setItem('listId', response.id))
  else
    $scope.list = List.get({id: listId});


  $scope.search = () ->
    Suggest.address({query: $scope.query})
    .$promise
    .then (response) ->
      $scope.searchResult = response;


  $scope.sortableOptions =
    update: (e, ui)->
      $scope.pushItems()


  $scope.pushItems = () ->
    console.log 'push', $scope, $scope.list, $scope.list.id
    $data = ({
      id: $scope.list.id,
      key: $scope.list.id,
      list: {
        title: 'abrvalg1',
        items: $scope.list.todo_list_items
      }
    });
    List.up($data);

  $scope.calcRoute = () ->
    $scope.wayBuilding = true;
    List.way(id: $scope.list.id)
    .$promise
    .then((response) ->
      $scope.wayBuilding = false;
      ListData.ways = response;
      $scope.ways = response;
    , ->
      $scope.wayBuilding = true;
      alert('Ошибка расчета'));

  $scope.addItem = (item) ->
    listItem = {
      "key": "string",
      "title": item.title,
      "type": "geo_point",
      "position": 0,
      "after": 0,
      "before": "string",
      "lon": if item.location then item.location.lon else item.lon,
      "lat": if item.location then item.location.lat else item.lat,
    }

    $scope.list.todo_list_items.push(listItem);
    $scope.query = ''
    $scope.pushItems()


  $scope.delItem = (index) ->
    $scope.list.todo_list_items.splice(index, 1);
    $scope.pushItems();
