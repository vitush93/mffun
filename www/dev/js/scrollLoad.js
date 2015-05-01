var ScrollLoad = function (container, loader, moreButton, endmsg, bottomOffset, interval, initialPage) {
    this.lock = false;
    this.bottomOffset = bottomOffset;
    this.currentPage = initialPage;
    this.interval = interval;
    this.container = container;
    this.loader = loader;
    this.moreButton = moreButton;
    this.endmsg = endmsg;
    this.reloadButtonContent = $('#reload-button-content');
    this.init();
};

ScrollLoad.prototype = {

    load: function (page) {
        var context = this;
        $.ajax({
            type: 'GET',
            url: window.location.toString(),
            data: 'do=load&page=' + page,
            timeout: 5000,
            error: function (x, t, m) {
                $(context.loader).hide();
                context.reloadButtonContent.show();
                context.reload(this);
            }
        }).done(function (data) {
            context.appendData(data);
        });
    },

    appendData: function (data) {
        var $loader = $(this.loader);
        var $more = $(this.moreButton);
        var $container = $(this.container);
        var $endmsg = $(this.endmsg);
        $container.append(data);
        if (data.more) {
            $loader.hide();
            $more.show();
            this.lock = true;
        } else if (data.nomore) {
            $loader.hide();
            $endmsg.show();
            this.lock = true;
        } else {
            this.lock = false;
            this.currentPage++;
        }
    },

    reload: function (ajaxContext) {
        var context = this;
        $('body').on('click', '#reload-button', function () {
            context.reloadButtonContent.hide();
            $(context.loader).show();
            $.ajax(ajaxContext).done(function (data) {
                context.appendData(data);
            });
        });
    },

    init: function () {
        var context = this;

        setInterval(function () {
            if (context.lock) return;
            var diff = document.body.offsetHeight - (window.innerHeight + window.scrollY);
            if (diff < context.bottomOffset) {
                context.lock = true;
                context.load(context.currentPage);
            }
        }, this.interval);
    }
};