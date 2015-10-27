var _ = require('underscore');
var BaseView = require('./BaseView');
var QuoteView = require('./QuoteView');
var Templates = require('../templates');
var EndlessScroll = require('../helpers/EndlessScroll');
var config = require('../config');

var QuotesView = function ($el, action, id, page) {
    this.$el = $el;

    if (action) {
        this.action = action;
    }

    if (id) {
        this.id = id;
    }

    if (page) {
        this.page = page;
    }

    this.init();
};

QuotesView.prototype = Object.create(BaseView.prototype);
QuotesView.prototype.constructor = QuotesView;

QuotesView.prototype.$loaderContainer = null;

QuotesView.prototype.endlessScroll = null;

QuotesView.prototype.currentLoad = 1;

QuotesView.prototype.action = 'default';

QuotesView.prototype.id = null;

QuotesView.prototype.page = 1;

QuotesView.prototype.init = function () {
    this.$loaderContainer = $('#js-loader-container');

    this.bindScrollLoad();
    this.bindRetry();
};

QuotesView.prototype.addQuoteViews = function (data) {
    var _this = this;
    _.each(data, function (quote) {
        var view = new QuoteView(Templates.quote, _this.$el, quote.id);

        view.render(quote);
    });
};

QuotesView.prototype.render = function (data) {
    this.addQuoteViews(data);
};

QuotesView.prototype.bindScrollLoad = function () {
    var _this = this;

    _this.loaderSpinner();

    _this.endlessScroll = new EndlessScroll();
    _this.endlessScroll.callback = function () {
        if (_this.currentLoad == config.endlessScroll.maxLoadsPerPage) {
            _this.loaderMore();

            return;
        }

        $.getJSON(config.api.quotes(_this.action, config.endlessScroll.itemsPerLoad, _this.getOffset(), _this.id), function (data) {
            if (data.length < 10) {
                _this.loaderEnd();

                _this.endlessScroll.lock = true;
                if (data.length == 0) return;
            } else {
                _this.loaderSpinner();
            }

            _this.addQuoteViews(data);

            _this.currentLoad++;

            _this.endlessScroll.lock = false;
        }).error(function () {
            _this.loaderError();
        });
    }
};

QuotesView.prototype.loaderEnd = function () {
    this.$loaderContainer.html(Templates.end());
};

QuotesView.prototype.loaderMore = function () {
    this.$loaderContainer.html(Templates.more({
        action: this.action,
        id: this.id,
        page: (1 + Number(this.page))
    }));
};

QuotesView.prototype.loaderError = function () {
    this.$loaderContainer.html(Templates.reload());
};

QuotesView.prototype.loaderSpinner = function () {
    this.$loaderContainer.html(Templates.loader());
};

QuotesView.prototype.getOffset = function () {
    var itemsPerPage = config.endlessScroll.itemsPerLoad * config.endlessScroll.maxLoadsPerPage;
    var pageOffset = (this.page - 1) * itemsPerPage;
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