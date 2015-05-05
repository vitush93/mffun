var $body = $('body');

$body.on('click', '.action', function (e) {
    var id = $(this).data('id');
    var signal = $(this).data('do');

    var $container = $('#action-q-' + id);
    var $row = $('#row-q-' + id);

    $.ajax({
        type: 'GET',
        url: window.location.toString(),
        data: 'do=' + signal + '&qid=' + id
    }).done(function (data) {
        if (data.hide) {
            $row.remove();
        } else {
            $container.html(data);
        }
    });
});