var _ = require('underscore');
var BaseView = require('./BaseView');
var QuoteView = require('./QuoteView');
var Template = require('../templates');

var QuotesView = function ($el) {
    this.$el = $el;
    this.quoteView = new QuoteView();
};

QuotesView.prototype = BaseView.prototype;

/**
 * Child view for single quote.
 *
 * @type {null|QuoteView}
 */
QuotesView.prototype.quoteView = null;

QuotesView.prototype.render = function (data) {
    var html = '';

    var _this = this;
    _.each(data, function (quote) {
        html += _this.quoteView.template(quote);
    });

    this.$el.html(html);
};

module.exports = QuotesView;