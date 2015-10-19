require('jquery');

var CommentRating = require('../../models/CommentRating');
var QuoteRating = require('../../models/QuoteRating');

/**
 * @deprecated
 */
module.exports = function () {

    new QuoteRating('.q-rate', '.q-rate-up', '.q-rate-down', function (data) {
        $('#q-rating-' + data.qid).html(data.rating);
    });

    new CommentRating('.rate.up', '.rate.down', function (data) {
        $('#c-ups-' + data.cid).html(data.ups);
        $('#c-downs-' + data.cid).html(data.downs);
    });
};