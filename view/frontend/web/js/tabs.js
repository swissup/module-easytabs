define([
    'jquery',
    'mage/accordion',
    'Magento_Ui/js/lib/view/utils/async'
], function ($, accordion) {
    'use strict';

    /**
     * Animated scroll to element
     *
     * @param  {jQuery} element
     */
    function _scrollAnimated(element) {
        $('html, body').animate({
            scrollTop: element.offset().top - 50
        }, 300);
    }

    /**
     * @param  {jQuery} context
     */
    function _updateFormKey(context)
    {
        var inputSelector = 'input[name="form_key"]',
            formKey;

        formKey = $.mage.cookies.get('form_key');
        $(inputSelector, context).val(formKey);
    }

    $.widget('swissup.tabs', accordion, {
        options: {
            externalLink: '[data-action=activate-tab], .action[href*="\\#review"]',
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
            this._bindBeforeOpen();
            this.lastOpened = this.getOpened();
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
                    // Trigger mage-init execution.
                    content.trigger('contentUpdated');
                    // apply ko binding
                    content.children().applyBindings();
                    // Unset height for tab content.
                    content.css('height', '');
                    // Trigger event that content is loaded.
                    content.trigger('easytabs:contentLoaded');
                    // Update formkey
                    _updateFormKey(content);
                }
            );
        },

        /**
         * Listen external link click to activate tab.
         */
        _bindExternalLinks: function () {
            var that = this;

            $(that.options.externalLink).on('click', function (event) {
                var anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');

                // Workaround to open reviews tab when click on link under product image.
                anchor = anchor === 'review-form' ? 'reviews' : anchor;
                $('[data-role="content"]', that.element).each(function (index) {
                    if (this.id === anchor) {
                        that.element.tabs('activate', index);
                        _scrollAnimated($(this));
                        event.preventDefault();
                        event.stopImmediatePropagation();
                    }
                });
            });

            // force my click listener to run first
            // inspired by https://stackoverflow.com/a/13980262
            $(that.options.externalLink).each(function () {
                var handlers;

                handlers = jQuery._data(this).events.click;
                handlers.unshift(handlers.pop());
            });
        },

        /**
         * Listen tab before open.
         */
        _bindBeforeOpen: function () {
            var that = this;

            this.collapsibles.on('beforeOpen', function (event) {
                var currentTab = $(event.currentTarget),
                    height;

                height = that.getContent(that.lastOpened).outerHeight();
                that.lastOpened = $(event.currentTarget);

                if (that.isAjaxTab(currentTab) &&
                    $(window).width() > 767
                ) {
                    // Tab has ajax content. Set height for the tab content to
                    // reduce jumps of page content.
                    that.getContent(currentTab).css('height', height);
                }
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
        },

        /**
         * {inheritdoc}
         */
        _instantiateCollapsible: function (element, index, active, disabled) {
            var content = this.contents.eq(index),
                anchor = window.location.hash;

            this._super(element, index, active, disabled);

            // Expand tab and scroll to it.
            try {
                if (content.find(anchor).length > 0 ||
                    content.attr('id') === anchor.replace('#', '')
                ) {
                    $(element).collapsible('forceActivate');
                    _scrollAnimated(
                        content.find(anchor).length ? content.find(anchor) : content
                    );
                }
            } catch (err) {
                console.warn(err);
            }
        },

        /**
         * Get opened tab.
         *
         * @return {jQuery}
         */
        getOpened: function () {
            var that = this;

            return that.collapsibles.filter(function () {
                return $(this).hasClass(that.options.openedState);
            });
        },

        /**
         * Get content of tab (collapsibles).
         *
         * @param  {HTMLElement} element
         * @return {jQuery}
         */
        getContent: function (element) {
            var collapsible = $(element).data('mageCollapsible');

            if (!collapsible) {
                return $();
            }

            return $(collapsible.content);
        },

        /**
         * Check if tab loads content via ajax.
         *
         * @param  {HTMLElement}  element
         * @return {Boolean}
         */
        isAjaxTab: function (element) {
            return !!$(this.options.ajaxUrlElement, element).length;
        }
    });

    return $.swissup.tabs;
});
