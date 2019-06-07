define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
        var tabsContainer = $(config.tabsSelector);

        $(config.linksSelector).on('click', function (event) {
            var anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');

            event.preventDefault();
            $('[data-role="content"]', tabsContainer).each(function (index) {
                if (this.id === anchor) {
                    tabsContainer.tabs('activate', index);
                    $('html, body').animate({
                        scrollTop: $(this).offset().top - 50
                    }, 300);
                }
            });
        });
    };
});
