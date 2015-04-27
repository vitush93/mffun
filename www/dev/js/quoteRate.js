/**
 *
 * @param rating commong rate up/down selection
 * @param up rate up button selector
 * @param down rate down button selector
 * @param callback after-rate callback
 * @constructor
 */
var QuoteRating = function (rating, up, down, callback) {
    this.rating = rating;
    this.callback = callback;
    this.up = up;
    this.down = down;

    this.init();
};

QuoteRating.prototype = {

    /**
     * Locks / unlocks rating.
     * Performs ajax request to rate quote by given id.
     *
     * @param qid ID of the quote
     * @param rate up/down
     */
    rate: function (qid, rate) {
        var context = this;

        $.ajax({
            method: 'GET',
            url: window.location.toString(),
            data: 'rateQuote-qid=' + qid + '&do=rateQuote-' + rate
        }).done(function (data) {
            context.callback(data);
        });
    },

    /**
     * Toggles 'active' class on rate up/down button.
     *
     * @param $elem jquery element
     */
    activeToggle: function ($elem) {
        var qid = $elem.data('qid');
        if ($elem.hasClass('active')) {
            $elem.removeClass('active');
        } else {
            $('a[data-qid=' + qid + ']').removeClass('active');
            $elem.addClass('active');
        }
    },

    /**
     * Attaches class toggling and ajax requesting on button click.
     *
     * @param selector rate up/down button selector
     */
    attach: function (selector) {
        var context = this;
        $('body').on('click', selector, function (e) {
            var $el = $(this);

            e.preventDefault();
            context.activeToggle($el);
            context.rate($el.data('qid'), $el.data('rate'));
        });
    },

    init: function () {
        this.attach(this.up);
        this.attach(this.down);
    }
};

var common = '.q-rate';
var up = '.q-rate-up';
var down = '.q-rate-down';

var r = new QuoteRating(common, up, down, function (data) {
    $('#q-rating-' + data.qid).html(data.rating);
});