var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.less('styles.less');
    mix.coffee('**/*.coffee');
});
