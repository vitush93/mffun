'use strict';

var Backbone = require('backbone');
var HomeController = require('../controllers/HomeController');

module.exports = Backbone.Router.extend({

    routes: {
        '': 'homeRoute'
    },

    homeRoute: function () {
        // HomeController();
    }

});
