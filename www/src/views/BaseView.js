
var BaseView = function ($el) {
    this.$el = $el;
};

BaseView.prototype.template = function () {
    throw ('view template is not set');
};

BaseView.prototype.render = function () {
    throw ('view render function not implemented');
};

BaseView.prototype.$el = null;

module.exports = BaseView;