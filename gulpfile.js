var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
   	mix.styles([
        'foundation.css',
        'motion-ui.css',
        'foundation-flex.css',
        'foundation-icons.css',
        'selectize.css',
        'main.css'
    ])
    .scripts([
        'jquery.js',
        'foundation.js',
        'masonry.pkgd.js',
        'selectize.js',
        'main.js'
    ])
    .version(['css/all.css', 'js/all.js']);
});
