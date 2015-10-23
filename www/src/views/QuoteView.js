var _ = require('underscore');
var BaseView = require('./BaseView');
var config = require('../config');

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

    this.bindRating();
};

QuoteView.prototype.hasRated = function (value) {
    //var _this = this; TODO use localStorage instead
    //return _.some(window.logged_user.q_ratings, function (item) {
    //    return item.q_id == _this.id && item.value == value;
    //});
};

QuoteView.prototype.render = function (data) {
    //data.rated_up = this.hasRated(1); TODO use localStorage instead
    //data.rated_down = this.hasRated(-1);

    data.logged_user = window.logged_user;

    var _this = this;
    $.getJSON('http://fun.dev/api/?presenter=Comment&action=quote&id=587', function (comments) {
        data.comments = comments;
        data.root_comments = comments[0];
        console.log(comments);

        var html = _this.template(data);

        _this.$el.html(html);
        _this.$container.append(_this.$el);
    });
};

QuoteView.prototype.bindRating = function () {
    var _this = this;
    this.$el.on('click', '.q-rate', function (e) {
        e.preventDefault();

        if (window.logged_user == undefined || window.logged_user.length == 0) {
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
