var $ = require('jquery');
var QuotesView = require('../views/QuotesView');
var Templates = require('../templates');
var KeyboardShortcuts = require('../bindings/globals/KeyboardShortcuts');

module.exports = function (action, id, page) {
    var view = new QuotesView($('#content-load'), action, id, page);
    view.render(window.quotes); // render initial quotes

    KeyboardShortcuts();
};