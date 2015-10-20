var $ = require('jquery');
var _ = require('underscore');
var QuotesView = require('../views/QuotesView');
var Templates = require('../templates');

var QuoteController = {

    view: null,

    init: function () {
        this.view = new QuotesView($('#content-load'));
        this.view.render(window.quotes); // render initial quotes
    }
};

module.exports = function () {
    QuoteController.init();
};