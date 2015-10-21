var _ = require('underscore');
var BaseView = require('./BaseView');
var QuoteView = require('./QuoteView');
var Templates = require('../templates');
var EndlessScroll = require('../helpers/EndlessScroll');
var config = require('../config');

var QuotesView = function ($el) {
    this.$el = $el;

    this.init();
};

QuotesView.prototype = Object.create(BaseView.prototype);
QuotesView.prototype.constructor = QuotesView;

QuotesView.prototype.quotes = [];

QuotesView.prototype.$loaderContainer = null;

QuotesView.prototype.endlessScroll = null;

QuotesView.prototype.currentLoad = 1;

QuotesView.prototype.init = function () {
    this.$loaderContainer = $('#js-loader-container');

    this.bindScrollLoad();
    this.bindRetry();
};

QuotesView.prototype.addQuoteViews = function (data) {
    var _this = this;
    _.each(data, function (quote) {
        var view = new QuoteView(_this.$el, quote.id);
        view.render(quote);

        _this.quotes.push(view);
    });
};

QuotesView.prototype.render = function (data) {
    this.addQuoteViews(data);
};

QuotesView.prototype.bindScrollLoad = function () {
    var _this = this;

    _this.endlessScroll = new EndlessScroll();
    _this.endlessScroll.callback = function () {
        if (_this.currentLoad == config.endlessScroll.maxLoadsPerPage) {
            _this.$loaderContainer.html(Templates.more({
                action: window.action,
                page: (1 + Number(_this.getPage()))
            }));

            return;
        }
        _this.$loaderContainer.html(Templates.loader());

        $.getJSON(config.api.quotes(_this.getAction(), config.endlessScroll.itemsPerLoad, _this.getOffset(), window.url_id), function (data) {
            if (data.length == 0) {
                _this.$loaderContainer.html(Templates.end());

                _this.endlessScroll.lock = true;
                return;
            }
            _this.addQuoteViews(data);

            _this.currentLoad++;

            _this.endlessScroll.lock = false;
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

QuotesView.prototype.bindRetry = function () {
    var _this = this;
    $('body').on('click', '#reload-button', function () {
        _this.endlessScroll.lock = false;
    });
};

module.exports = QuotesView;