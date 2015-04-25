var CommentRate = function (up, down, callback) {
    this.up = up;
    this.down = down;
    this.lock = [];
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
        $('a[data-cid=' + cid + "]").removeClass('active');
        $el.toggleClass('active');

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
        if (this.lock[cid] !== null) { // rating is locked
            if (this.lock[cid] != rate) { // user changed his mind?
                this.lock[cid] = null;
            } else { // user is clicking same button multiple times
                return;
            }
        }

        var context = this;
        $.ajax({
            method: 'GET',
            url: window.location.toString(),
            data: 'rateComment-cid=' + cid + '&do=rateComment-' + rate
        }).done(function (data) {
            context.callback(data);
            context.lock[cid] = rate;
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

var cr = new CommentRate('.rate.up', '.rate.down', function (data) {
    console.log(data);
});