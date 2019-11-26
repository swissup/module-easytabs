define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (Collapsible) {
        // Extend collapsible component with options:
        //   - ajaxUrlAttribute - attribute from ajaxUrlElement with url;
        //   - ajaxContentCache - cache ajax request;
        //   - ajaxContentOnce - make ajax request only once.
        Collapsible.prototype._loadContent = wrapper.wrap(
            Collapsible.prototype._loadContent,
            function (originalFunction) {
                var ajaxElement = this.element.find(this.options.ajaxUrlElement),
                    url = ajaxElement.attr(this.options.ajaxUrlAttribute);

                if (url) {
                    this.xhr = $.get({
                        url: url,
                        dataType: 'html',
                        cache: this.options.ajaxContentCache
                    }, function () {
                    });

                    if (this.options.ajaxContentOnce) {
                        ajaxElement.removeAttr(this.options.ajaxUrlAttribute);
                    }
                }

                return originalFunction();
            }
        );

        return Collapsible;
    };
});
