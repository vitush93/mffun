'use strict';

var Backbone = require('backbone');
var Quote = require('../models/Quote');

module.exports = Backbone.Collection.extend({
    url: 'http://mff.dev/',
    model: Quote
});
