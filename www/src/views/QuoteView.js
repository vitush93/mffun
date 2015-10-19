var _ = require('underscore');
var BaseView = require('./BaseView');
var Template = require('../templates');

var QuoteView = function ($el) {
    this.$el = $el;
    this.template = Template.quote;
};

QuoteView.prototype = BaseView.prototype;

QuoteView.prototype.render = function (data) {
    var html = this.template(data);

    this.$el.html(html);
};

module.exports = QuoteView;
