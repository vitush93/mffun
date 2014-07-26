var PageSlider = {

    // predany event (keydown standardne)
    event: null,

    // hlavni element, velky horizontalni div
    element: null,

    // aktualni stranka (velky horizontalni div)
    currentPageIndex: 0,

    // na pozici this.partialPagesIndexes[this.currentPageIndex] je ulozen aktualni stav vertikalniho divu
    // index 0 je sluzby, index 1 jsou reference
    partialPagesIndexes: [0, 0],

    // pohne s casti hlavniho divu dolu
    // najit vnitrni div pres find
    moveDown: function (part) {
        // TODO
        part.animate({bottom: 10});
        // zde kontrola vyjeti
        this.partialPagesIndexes[this.currentPageIndex]++;
    },

    // pohne s casti hlavniho divu nahoru
    // najit vnitrni div pres find
    moveUp: function (part) {
        // TODO
        part.animate({top: 10});
        // zde kontrola vyjeti
        this.partialPagesIndexes[this.currentPageIndex]--;
    },

    //animuje doleva
    moveLeft: function () {
        // TODO cokoliv
        this.element.animate({left: 200});
    },

    //animuje doprava
    moveRight: function () {
        // TODO cokoliv
        this.element.animate({right: 200});
    },

    // tohle pripravi k pouziti celou tridu
    init: function (e, element) {
        this.event = e;
        this.element = element;
        this.bootstrap(e.key);
    },

    // tohle je rozhodovaci system, podle cudliku to spousti funkce
    bootstrap: function (keyId) {
        if (keyId == 1) {
            this.moveLeft();
        } else if (keyId == 2) {
            this.moveRight();
        } else if (keyId == 3) {
            this.moveDown(this.element.find('vertical-handler'+this.currentPageIndex).find('vertical-div'));
        } else if (keyId == 4) {
            this.moveUp(this.element.find('vertical-handler'+this.currentPageIndex).find('vertical-div'));
        }
    }
}

$(function () {
    $(document).on('keydown', function (e) {
        PageSlider.init(e, $('#myBigDiv'));
    })
});

