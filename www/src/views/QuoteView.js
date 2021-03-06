var _ = require('underscore');
var BaseView = require('./BaseView');
var config = require('../config');
var UserStorage = require('../helpers/UserStorage');

var QuoteView = function (template, $el, id) {
    this.$container = $el;
    this.template = template;
    this.id = id;

    this.init();
};

QuoteView.prototype = Object.create(BaseView.prototype);
QuoteView.prototype.constructor = QuoteView;

QuoteView.prototype.id = 0;

QuoteView.prototype.init = function () {
    this.$el = $('<div></div>');
    this.$el.addClass('js-quote-view');

    this.rateQuote();
};

QuoteView.prototype.hasRated = function (value) {
    return UserStorage.ratedQuote(this.id, value);
};

QuoteView.prototype.render = function (data) {
    data.rated_up = this.hasRated(1);
    data.rated_down = this.hasRated(-1);

    data.base_url = window.base_url;
    var html = this.template(data);
    this.$el.html(html);
    this.$container.append(this.$el);
};

QuoteView.prototype.rateQuote = function () {
    var _this = this;
    this.$el.on('click', '.q-rate', function (e) {
        e.preventDefault();

        if (UserStorage.user == undefined) {
            window.location.href = "/sign/in";

            return;
        }

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

            UserStorage.rateQuote(_this.id, 1);
        }
    });
};

QuoteView.prototype.rateDown = function () {
    var _this = this;
    $.getJSON(config.api.rateDown(this.id), function (res) {
        if (res.success) {
            _this.$el.find('.js-q-rating').html(res.rating);

            UserStorage.rateQuote(_this.id, -1);
        }
    });
};

module.exports = QuoteView;
