window.$ = window.jQuery = require('jquery');

require('bootstrap');
require('./bindings');

var Backbone = require('backbone');
var AppRouter = require('./router/AppRouter');

$(function () {
    new AppRouter();

    Backbone.history.start({
        pushState: true
    });
});