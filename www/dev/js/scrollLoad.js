var ScrollLoad = function (container, loader, moreButton, endmsg, bottomOffset, interval, initialPage) {
    this.lock = false;
    this.bottomOffset = bottomOffset;
    this.currentPage = initialPage;
    this.interval = interval;
    this.container = container;
    this.loader = loader;
    this.moreButton = moreButton;
    this.endmsg = endmsg;
    this.init();
};

ScrollLoad.prototype = {

    load: function (page) {
        var $loader = $(this.loader);
        var $more = $(this.moreButton);
        var $container = $(this.container);
        var $endmsg = $(this.endmsg);
        var context = this;
        $.ajax({
            type: 'GET',
            url: window.location.toString(),
            data: 'do=load&page=' + page
        }).done(function (data) {
            $container.append(data);
            if (data.more) {
                $loader.hide();
                $more.show();
                context.lock = true;
            } else if (data.nomore) {
                $loader.hide();
                $endmsg.show();
                context.lock = true;
            } else {
                context.lock = false;
                context.currentPage++;
            }
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