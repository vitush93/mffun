var $ = require('jquery');
var QuotesView = require('../views/QuotesView');
var Templates = require('../templates');

var QuoteController = {

    view: null,

    init: function () {
        this.view = new QuotesView($('#content-load'), window.quotes);
        this.view.render(); // render initial quotes
    }
};

module.exports = function () {
    QuoteController.init();
};