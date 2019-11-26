define([
    'jquery',
    'mage/accordion',
    'Magento_Ui/js/lib/view/utils/async'
], function ($, accordion) {
    'use strict';

    $.widget('swissup.tabs', accordion, {
        options: {
            externalLink: '[data-action=activate-tab], .action[href$="\\#review-form"]',
            ajaxUrlElement: '[data-ajaxurl]',
            ajaxUrlAttribute: 'data-ajaxurl',
            ajaxContentOnce: true,
            ajaxContentCache: true
        },

        /**
         * {@inheritdoc}
         */
        _create: function () {
            // Compatibility with old customizations.
            // Someday can be removed.
            if (typeof this.options.active === 'number') {
                this.options.active = [this.options.active];
            }

            this._bindAfterAjax();
            this._super();
            this._bindExternalLinks();
        },

        /**
         * Listen tab content update after Ajax request.
         */
        _bindAfterAjax: function () {
            var that = this;

            $.async(
                {
                    selector: '[data-role=content][aria-busy=true] > :first-child',
                    ctx: this.element.get(0)
                },
                function (firstChild) {
                    var content = $(firstChild).parent();

                    that._cancelFurtherPromiseCalls(content);
                    content.trigger('contentUpdated');
                }
            );
        },

        /**
         * Listen external link click to activate tab.
         */
        _bindExternalLinks: function () {
            var that = this;

            $(this.options.externalLink).on('click', function (event) {
                var anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');

                event.preventDefault();
                // Workaround to open reviews tab when click on link under product image.
                anchor = anchor === 'review-form' ? 'reviews' : anchor;
                $('[data-role="content"]', that.element).each(function (index) {
                    if (this.id === anchor) {
                        that.element.tabs('activate', index);
                        $('html, body').animate({
                            scrollTop: $(this).offset().top - 50
                        }, 300);
                    }
                });
            });
        },

        /**
         * @param  {jQuery} content
         */
        _cancelFurtherPromiseCalls: function (content) {
            var tabTitleId = content.attr('aria-labelledby'),
                title;

            if (tabTitleId) {
                title = document.getElementById(tabTitleId);
                // cancel follow requests for tab
                delete $(title).data('mageCollapsible').xhr;
            }
        }
    });

    return $.swissup.tabs;
});
