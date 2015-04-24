var autocomplete = {
    create: function (selector, url) {
        var cache = {};
        $(selector).autocomplete({
            source: function (request, response) {
                var term = request.term;
                if (term in cache) {
                    var results = $.ui.autocomplete.filter(cache[term], term);
                    response(results.slice(0, 10));

                    return;
                }

                $.getJSON(window.location.pathname + url, request, function (data, status, xhr) {
                    cache[term] = data;

                    var results = $.ui.autocomplete.filter(data, term);
                    response(results.slice(0, 10));
                });
            }
        });
    }
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
                        console.log("not found");
                        response({});
                    } else {
                        console.log("found");
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