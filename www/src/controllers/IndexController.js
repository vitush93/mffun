var $ = require('jquery');
var QuotesView = require('../views/QuotesView');

module.exports = function (action, id) {
    console.log('action: ' + action);
    console.log('id: ' + id);

    var view = new QuotesView($('#content-load'));
    view.render(window.quotes); // render initial quotes
};