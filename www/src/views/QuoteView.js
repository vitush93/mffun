var BaseView = require('./BaseView');
var Templates = require('../templates');
var config = require('../config');

var QuoteView = function ($el, id) {
    this.$container = $el;
    this.template = Templates.quote;
    this.id = id;

    this.init();
};

QuoteView.prototype = Object.create(BaseView.prototype);
QuoteView.prototype.constructor = QuoteView;

QuoteView.prototype.id = 0;

QuoteView.prototype.init = function () {
    this.$el = $('<div></div>');
    this.$el.addClass('js-quote-view');

    this.bindRating();
};

QuoteView.prototype.render = function (data) {
    var html = this.template(data);

    this.$el.html(html);
    this.$container.append(this.$el);
};

QuoteView.prototype.bindRating = function () {
    var _this = this;
    this.$el.on('click', '.q-rate', function (e) {
        e.preventDefault();

        if ($(this).hasClass('q-rate-up')) {
            _this.rateUp();
        } else {
            _this.rateDown();
        }

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            _this.$el.find('.q-rate').removeClass('active');
            $(this).toggleClass('active');
        }
    });
};

QuoteView.prototype.rateUp = function () {
    var _this = this;
    $.getJSON(config.api.rateUp(this.id), function (res) {
        if (res.success) {
            _this.$el.find('.js-q-rating').html(res.rating);
        }
    });
};

QuoteView.prototype.rateDown = function () {
    var _this = this;
    $.getJSON(config.api.rateDown(this.id), function (res) {
        if (res.success) {
            _this.$el.find('.js-q-rating').html(res.rating);
        }
    });
};

module.exports = QuoteView;
