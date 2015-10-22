window.$ = window.jQuery = require('jquery');

require('bootstrap');
require('./bindings/globals');

var Backbone = require('backbone');

var AppRouter = Backbone.Router.extend({
    routes: {

        '': 'homeRoute',

        'homepage/:action': 'homeRoute',

        'homepage/:action/:id': 'homeRoute',

        'quote/default/:id': 'quoteRoute',

        'test/neco': 'test'
    },

    quoteRoute: require('./controllers/QuoteController'),

    homeRoute: require('./controllers/IndexController')
});


var router = new AppRouter();
Backbone.history.start({
    pushState: true,
    root: '/'
});

$(document).ready(function () {
    var url = window.location.pathname;
    url = url.substr(1);

    router.navigate(url, {trigger: true});
});
