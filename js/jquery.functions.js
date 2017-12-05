jQuery(window).load(function() {
    if (jQuery('.ecard-carousel').length) {
        var elem = document.querySelector('.ecard-inner-container');
        var flkty = new Flickity( elem, {
            cellAlign: 'left',
            contain: true,
            freeScroll: true,
        });
    }

    if (jQuery('.ecard-masonry').length) {
        var container = document.querySelector('.ecard-inner-container');
        var msnry = new Masonry(container, {
            itemSelector: '.ecard',
            columnWidth: '.ecard',
            horizontalOrder: true,
        });
    }
});
