var $ = require('jquery');
var QuotesView = require('../views/QuotesView');
var Templates = require('../templates');
var KeyboardShortcuts = require('../bindings/globals/KeyboardShortcuts');

module.exports = function (action, id) {
    var view = new QuotesView($('#content-load'));
    view.render(window.quotes); // render initial quotes

    KeyboardShortcuts();
};