var keyboardShort = function() {

    var $sections = $('.article'),
        $animContainer = $('html, body'),
        $document = $(document),
        numSections = $sections.length,
        currSection = 0,
        isAnimating = false;

    var refreshVars = function () {
        $sections = $($sections.selector);
        numSections = $sections.length;
        isAnimating = false;
    };

    // Animate to a specific index.
    var gotoSection = function (index) {
        refreshVars();
        isAnimating = true;
        $animContainer.animate({
            scrollTop: $sections.eq(index).offset().top - 60
        }, 100, function () {
            isAnimating = false;
        });
    };

    // Advance to next or previous section.
    var handleAction = function (direction) {
        if (isAnimating) {
            return false;
        }

        if (direction === 'prev' && currSection > 0) {
            currSection--;
        } else if (direction === 'next' && currSection < numSections - 1) {
            currSection++;
        } else {
            return false;
        } // This shouldn't happen.

        gotoSection(currSection);
    };

    //// Handle clicks.
    //$document.on('click', '.action', function() {
    //    handleAction($(this).data('direction'));
    //});

    // Handle keyboard input.
    $document.keyup(function (e) {
        var el = e.currentTarget.activeElement.tagName;
        if (el == 'INPUT' || el == 'TEXTAREA') return;
        if (e.keyCode === 75 || e.keyCode == 37) {
            handleAction('prev');
        } // Up arrow.
        if (e.keyCode === 74 || e.keyCode == 39) {
            handleAction('next');
        } // Down arrow.
    });

};
