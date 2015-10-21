var BaseView = require('./BaseView');
var Templates = require('../templates');

var QuoteView = function ($el) {
    this.$container = $el;
    this.template = Templates.quote;

    this.init();
};

QuoteView.prototype = Object.create(BaseView.prototype);
QuoteView.prototype.constructor = QuoteView;

QuoteView.prototype.init = function () {
    this.$el = $('<div></div>');
    this.$el.addClass('js-quote-view');
};

QuoteView.prototype.render = function (data) {
    var html = this.template(data);

    this.$el.html(html);
    this.$container.append(this.$el);
};

module.exports = QuoteView;
