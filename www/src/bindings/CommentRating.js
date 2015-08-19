'use strict';

require('jquery');

var CommentRate = function (up, down, callback) {
    this.up = up;
    this.down = down;
    this.callback = callback;

    this.init();
};

CommentRate.prototype = {

    /**
     * Attaches active class toggling on rate up/down buttons.
     *
     * @param $el
     */
    activeToggle: function ($el) {
        var cid = $el.data('cid');
        if ($el.hasClass('active')) {
            $el.removeClass('active');
        } else {
            $('a[data-cid=' + cid + "]").removeClass('active');
            $el.addClass('active');
        }
    },

    /**
     * Attaches active class toggling and actual rate requesting.
     *
     * @param selector
     */
    attach: function (selector) {
        var context = this;
        $('body').on('click', selector, function (e) {
            e.preventDefault();
            var $el = $(this);

            context.activeToggle($el);
            context.rate($el.data('cid'), $el.data('rate'));
        });
    },

    /**
     * Does the rating via ajax request.
     *
     * @param cid
     * @param rate
     */
    rate: function (cid, rate) {
        var context = this;
        $.ajax({
            method: 'GET',
            url: window.location.toString(),
            data: 'rateComment-cid=' + cid + '&do=rateComment-' + rate
        }).done(function (data) {
            context.callback(data);
        });
    },

    /**
     * Prepares stuff.
     */
    init: function () {
        this.attach(this.up);
        this.attach(this.down);
    }
};

module.exports = function () {
    new CommentRate('.rate.up', '.rate.down', function (data) {
        $('#c-ups-' + data.cid).html(data.ups);
        $('#c-downs-' + data.cid).html(data.downs);
    });
};

