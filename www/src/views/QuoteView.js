var BaseView = require('./BaseView');

var QuoteView = function (template, $el, model) {
    this.template = template;
    this.$el = $el;
    this.model = model;
};

QuoteView.prototype = BaseView.prototype;

QuoteView.prototype.render = function () {
    var html = this.template(this.model);

    this.$el.append(html);
};

module.exports = QuoteView;
