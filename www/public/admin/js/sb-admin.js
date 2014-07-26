$(function () {
    $.nette.init();
    $('#side-menu').metisMenu();

    var barvy = [];
    $('.barva').click(function () {
        $(this).toggleClass('active');
        var val = $(this).data('id');
        if ($(this).hasClass('active')) {
            barvy.push(val);
        } else {
            barvy.splice(barvy.indexOf(val));
        }
        $('.barva-input').val(barvy.join());
    });
    $('#tagcloud span').click(function () {
        var tag = $(this).data('value');
        var input = $('.tags-autocomplete').val();
        if (input.length == 0) {
            $('.tags-autocomplete').val(tag);
        } else {
            $('.tags-autocomplete').val(input + ', ' + tag);
        }
        $(this).remove();
    });

    $('#sizecloud span').click(function () {
        var tag = $(this).data('value');
        var input = $('.velikost-input').val();
        if (input.length == 0) {
            $('.velikost-input').val(tag);
        } else {
            $('.velikost-input').val(input + ', ' + tag);
        }
        $(this).remove();
    });

    $('.varianty').hide();
    $('.varianty').first().show();
    $('#var-new').click(function () {
        $('.varianty:visible').next().show();
    });

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
$(function () {
    $(window).bind("load resize", function () {
        if ($(this).width() < 768) {
            $('div.sidebar-collapse').addClass('collapse')
        } else {
            $('div.sidebar-collapse').removeClass('collapse')
        }
    })
})
