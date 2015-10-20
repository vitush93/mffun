var _ = require('underscore');
var BaseView = require('./BaseView');
var QuoteView = require('./QuoteView');
var Templates = require('../templates');
var EndlessScroll = require('../helpers/EndlessScroll');
var config = require('../config');

var QuotesView = function ($el) {
    this.$el = $el;
    this.quoteView = new QuoteView();

    this.$loaderContainer = $('#js-loader-container');

    this.bindScrollLoad();
};

QuotesView.prototype = BaseView.prototype;

/**
 * Child view for single quote.
 *
 * @type {null|QuoteView}
 */
QuotesView.prototype.quoteView = null;

QuotesView.$loaderContainer = null;

QuotesView.prototype.render = function (data) {
    var html = '';

    var _this = this;
    _.each(data, function (quote) {
        html += _this.quoteView.template(quote);
    });

    this.$el.append(html);
};

QuotesView.prototype.currentLoad = 1;

QuotesView.prototype.bindScrollLoad = function () {
    var _this = this;

    var scroll = new EndlessScroll();
    scroll.callback = function () {
        if (_this.currentLoad == config.endlessScroll.maxLoadsPerPage) {
            _this.$loaderContainer.html(Templates.more({
                action: window.action,
                page: (1 + Number(_this.getPage()))
            }));

            return;
        }
        _this.$loaderContainer.html(Templates.loader());

        $.getJSON('/api/quote/' + _this.getAction() + '/?limit=' + config.endlessScroll.itemsPerLoad + '&offset=' + _this.getOffset(), function (data) {
            _this.render(data);

            _this.currentLoad++;

            scroll.lock = false;
            _this.$loaderContainer.html('');
        }).error(function () {
            _this.$loaderContainer.html(Templates.reload());
        });
    }
};

QuotesView.prototype.getAction = function () {
    return window.action;
};

QuotesView.prototype.getPage = function () {
    if (window.page.length == 0) {
        return 1;
    }

    return window.page;
};

QuotesView.prototype.getOffset = function () {
    var itemsPerPage = config.endlessScroll.itemsPerLoad * config.endlessScroll.maxLoadsPerPage;
    var pageOffset = (this.getPage() - 1) * itemsPerPage;
    var loadOffset = (this.currentLoad * config.endlessScroll.itemsPerLoad);

    return pageOffset + loadOffset;
};

module.exports = QuotesView;