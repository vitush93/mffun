var Backbone = require('backbone');

var AppRouter = Backbone.Router.extend({

    routes: {

        '': 'homeRoute',

        'homepage/:action': 'homeRoute',

        'homepage/:action/:id': 'homeRoute',

        'quote/default/:id': 'quoteRoute'
    },

    initialize: function () {
        this.bind('route', require('./UserMiddleware'));
    },

    quoteRoute: require('../controllers/QuoteController'),

    homeRoute: require('../controllers/IndexController')
});

module.exports = AppRouter;