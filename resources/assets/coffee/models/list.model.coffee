ListModel = ($resource, $location)->
  path = 'http://' + $location.host();
  $resource(path + '/api/list/:id/:type', {id: '@id', type: '@type'},
    {
      get: {method: 'GET', isArray: false},
      create: {method: 'POST', isArray: false},
      up: {method: 'POST', isArray: false, params: {type: 'update'}},
      way: {method: 'GET', isArray: false, params: {type: 'way'}},
    });
