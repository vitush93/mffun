var Backbone = require('backbone');
var IndexController = require('../controllers/IndexController');
var QuoteController = require('../controllers/QuoteController');

var AppRouter = Backbone.Router.extend({

    routes: {

        '': 'homeRoute',

        '?p=:page': 'homeRoute',

        'homepage/:action': 'actionRoute',

        'homepage/:action?p=:page': 'actionRoute',

        'homepage/:action/:id': 'actionIdRoute',

        'homepage/:action/:id?p=:page': 'actionIdRoute',

        'quote/default/:id': 'quoteRoute'
    },

    navigate: function (url) {
        Backbone.history.loadUrl(url);
    },

    initialize: function () {
        this.bind('route', require('./UserMiddleware'));
    },

    quoteRoute: QuoteController,

    homeRoute: function (page) {
        if (page) {
            page = this.parsePage(page);
        }

        IndexController(null, null, page);
    },

    actionRoute: function (action, page) {
        if (page) {
            page = this.parsePage(page);
        }

        IndexController(action, null, page);
    },

    actionIdRoute: function (action, id, page) {
        if (page) {
            page = this.parsePage(page);
        }

        IndexController(action, id, page);
    },

    parsePage: function (page) {
        page = page.split('=');

        return page[1];
    }

});

module.exports = AppRouter;