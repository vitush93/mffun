var autocomplete = {
    create: function (selector, url, customClass) {
        if (!customClass) customClass = 'cst';
        var cache = {};
        $(selector).autocomplete({
            source: function (request, response) {
                var term = request.term;
                if (term in cache) {
                    var results = $.ui.autocomplete.filter(cache[term], term);
                    response(results.slice(0, 5));

                    return;
                }

                $.getJSON(window.location.pathname + url, request, function (data, status, xhr) {
                    cache[term] = data;

                    var results = $.ui.autocomplete.filter(data, term);
                    response(results.slice(0, 5));
                });
            }
        }).autocomplete('widget').addClass(customClass);
    }
};
var cache = [];
var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/";

$("#page-search-box").autocomplete({
    source: function (request, response) {
        var term = request.term;
        if (term in cache) {
            var r = $.ui.autocomplete.filter(cache[term], term);
            response(r.slice(0, 5));
            return;
        }
        $.getJSON(window.location.pathname + "?do=searchJson", request, function (data, status, xhr) {
            cache[term] = data;

            var results = $.ui.autocomplete.filter(data, term);
            response(results.slice(0, 5));
        });
    },
    select: function (event, ui) {
        var target = baseUrl + "homepage/" + ui.item.type + "/" + ui.item.id;
        window.location.replace(target);
    },
    minLength: 1
}).autocomplete('widget').addClass('page-search');
$('#page-search-box').data("ui-autocomplete")._renderItem = function (ul, item) {
    var icon = "";
    if(item.type == 'subject') {
        icon = '<i class="fa fa-book"></i>';
    }
    if(item.type == 'teacher') {
        icon = '<i class="fa fa-user"></i>'
    }

    return $('<li class="result-item">')
        .append('<p>' + icon + item.value + '<span>' + item.desc + '</span></p>')
        .appendTo(ul);
};

var subjectAutocomplete = {
    init: function () {
        autocomplete.create(".subject-autocomplete", "?do=addQuoteControl-subjectJson");
    }
};

var teacherAutocomplete = {
    init: function () {
        autocomplete.create(".teacher-autocomplete", "?do=addQuoteControl-teacherJson");
    }
};

function split(val) {
    return val.split(/,\s*/);
}
function extractLast(term) {
    return split(term).pop();
}
var tagAutocomplete = {
    init: function () {
        var selected = [];
        $('.tags-autocomplete').autocomplete({
            source: function (request, response) {
                $.getJSON(window.location.pathname + "?do=addQuoteControl-tagsJson", request, function (data, status, xhr) {
                    var term = extractLast(request.term);

                    if (selected.indexOf(term) > -1) {
                        response({});
                    } else {
                        selected.push(term);
                        var results = $.ui.autocomplete.filter(data, term);

                        response(results.slice(0, 10));
                    }
                });
            },
            search: function () {
                // custom minLength
                var term = extractLast(this.value);
                if (term.length < 1) {
                    return false;
                }
            },
            focus: function () {
                // prevent value inserted on focus
                return false;
            },
            select: function (event, ui) {
                var terms = split(this.value);
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push(ui.item.value);
                // add placeholder to get the comma-and-space at the end
                terms.push("");
                this.value = terms.join(", ");
                return false;
            }
        });
    }
};


tagAutocomplete.init();
subjectAutocomplete.init();
teacherAutocomplete.init();