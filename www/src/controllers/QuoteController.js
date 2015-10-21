var $ = require('jquery');
var QuotesView = require('../views/QuotesView');
var Templates = require('../templates');

var QuoteController = {

    view: null,

    init: function () {
        if (window.quotes == undefined) return;

        this.view = new QuotesView($('#content-load'));
        this.view.render(window.quotes); // render initial quotes
    }
};

module.exports = function () {
    QuoteController.init();
};