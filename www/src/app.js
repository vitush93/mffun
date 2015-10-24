window.$ = window.jQuery = require('jquery');

require('bootstrap');
require('./bindings/globals');

var Backbone = require('backbone');
var AppRouter = require('./router/AppRouter');

$(function () {
    var router = new AppRouter();

    //Backbone.history.start({
    //    pushState: true,
    //    root: '/'
    //});

    Backbone.history.start();

    var url = window.location.pathname;
    //url = url.substr(1);

    //router.navigate(url, {trigger: true});
    router.navigate(url);
});