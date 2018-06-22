define([
    'jquery'
], function($) {
    'use strict';

    return function(config) {
        $(function() {
            var tabsContainer = $(config.tabsSelector);
            var links = $(config.linksSelector);

            links.on('click', function(event) {
                event.preventDefault();

                var anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');
                tabsContainer.children('[data-role="content"]').each(function(index) {
                    if (this.id == anchor) {
                        tabsContainer.tabs('activate', index);
                        $('html, body').animate({
                            scrollTop: $(this).offset().top - 50
                        }, 300);
                    }
                });
            });
        });
    };
});
