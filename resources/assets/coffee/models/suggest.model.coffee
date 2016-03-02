SugestModel = ($resource, $location) ->
  path = 'http://' + $location.host();
  $resource(path + '/api/suggest/address/:query', {query: '@query'},
    {
      address: {method: 'GET', isArray: true},
    });
