var $ = require('jquery');
var _ = require('underscore');

var EndlessScroll = function (callback, interval, bottomOffset) {
    if (_.isFunction(callback)) {
        this.callback = callback;
    } else if (!_.isUndefined(callback)) throw ('scroll callback is not a function');

    if (!_.isUndefined(interval)) {
        this.interval = interval;
    }

    if (!_.isUndefined(bottomOffset)) {
        this.bottomOffset = bottomOffset;
    }

    this.bind();
};

EndlessScroll.prototype.lock = false;

EndlessScroll.prototype.interval = 200;

EndlessScroll.prototype.bottomOffset = 1000;

EndlessScroll.prototype.callback = function () {
    this.lock = false;
};

EndlessScroll.prototype.bind = function () {
    var _this = this;
    setInterval(function () {
        if (_this.lock) return;

        var diff = $(document).height() - ($(window).height() + $(document).scrollTop());
        if (diff < _this.bottomOffset) {
            _this.lock = true;

            _this.callback();
        }
    }, this.interval);
};

module.exports = EndlessScroll;