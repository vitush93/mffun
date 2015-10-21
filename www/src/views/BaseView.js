
var BaseView = function () {

};

BaseView.prototype.template = function () {
    throw ('view template is not set');
};

BaseView.prototype.render = function () {
    throw ('view render function not implemented');
};

BaseView.prototype.init = function () {

};

BaseView.prototype.$el = null;

BaseView.prototype.model = null;

module.exports = BaseView;