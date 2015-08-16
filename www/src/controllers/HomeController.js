'use strict';

var $ = require('jquery');
var QuotesView = require('../views/QuotesView');

module.exports = function () {
    var view = new QuotesView();

    view.render();
};
