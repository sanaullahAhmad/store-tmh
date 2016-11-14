var elixir = require('laravel-elixir');
require('es6-promise').polyfill();

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
/////////////////////////////////MOBILE CSS///////////////////////////////////

elixir(function(mix) {
    mix.styles([
        "style.css",
        "slider-main.css",
        "angular-carousel.css",
        "device.css"
    ] , 'public/mobile/css/homepage.css', 'resources/assets/mobile/css');
});

elixir(function(mix) {
    mix.styles([
        "style.css",
        "device.css"
    ], 'public/mobile/css/common.css', 'resources/assets/mobile/css');
});

elixir(function(mix) {
    mix.styles([
        "category.css" 
    ], 'public/mobile/css/category.css', 'resources/assets/mobile/css');
});


elixir(function(mix) {
    mix.styles([
        "login.css" 
    ], 'public/mobile/css/login.css', 'resources/assets/mobile/css');
});



elixir(function(mix) {
    mix.styles([
        "account.css" 
    ], 'public/mobile/css/account.css', 'resources/assets/mobile/css');
});

elixir(function(mix) {
    mix.styles([
        "post_category.css"
    ], 'public/mobile/css/post_category.css', 'resources/assets/mobile/css');
});
elixir(function(mix) {
    mix.styles([
        "posts.css"
    ], 'public/mobile/css/posts.css', 'resources/assets/mobile/css');
});
elixir(function(mix) {
    mix.styles([
        "pages.css"
    ], 'public/mobile/css/pages.css', 'resources/assets/mobile/css');
});


/*

elixir(function(mix) {
    mix.scripts([
        "angular.js",
        "angular-touch.min.js",
        "angular-carousel.min.js",
        "products-slider.js",
        "app.js",
        "angular-ui-bootstrap/ui-bootstrap-tpls-0.12.1.min.js"
    ], 'public/js/master.js');
});
*/

/////////////////////////////////Desktop CSS///////////////////////////////////

elixir(function(mix) {
    mix.styles([
        "style.css",
        "slider-main.css",
        "angular-carousel.css",
        "device.css"
    ], 'public/desktop/css/homepage.css', 'resources/assets/desktop/css');
});

elixir(function(mix) {
    mix.styles([
        "style.css",
        "device.css"
    ], 'public/desktop/css/common.css', 'resources/assets/desktop/css');
});

elixir(function(mix) {
    mix.styles([
        "category.css"
    ], 'public/desktop/css/category.css', 'resources/assets/desktop/css');
});


elixir(function(mix) {
    mix.styles([
        "login.css"
    ], 'public/desktop/css/login.css', 'resources/assets/desktop/css');
});



elixir(function(mix) {
    mix.styles([
        "account.css"
    ], 'public/desktop/css/account.css', 'resources/assets/desktop/css');
});

elixir(function(mix) {
    mix.styles([
        "post_category.css"
    ], 'public/desktop/css/post_category.css', 'resources/assets/desktop/css');
});
elixir(function(mix) {
    mix.styles([
        "posts.css"
    ], 'public/desktop/css/posts.css', 'resources/assets/desktop/css');
});
elixir(function(mix) {
    mix.styles([
        "pages.css"
    ], 'public/desktop/css/pages.css', 'resources/assets/desktop/css');
});
