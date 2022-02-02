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
            var me = this;

            // Compatibility with old customizations.
            // Someday can be removed.
            if (typeof me.options.active === 'number') {
                me.options.active = [me.options.active];
            }

            me._bindAfterAjax();
            me._super();
            me.lastOpened = me.getOpened();
            me.lastOpened.trigger('beforeOpen');
            me._bindExternalLinks();
            me._bindBeforeOpen();
        },

        /**
         * Listen tab content update after Ajax request.
         */
        _bindAfterAjax: function () {
            var me = this;

            $.async(
                {
                    selector: '[data-role=content][aria-busy=true] > :first-child',
                    ctx: me.element.get(0)
                },
                function (firstChild) {
                    var content = $(firstChild).parent();

                    me._cancelFurtherPromiseCalls(content);
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
            var me = this;

            $(me.options.externalLink).on('click', function (event) {
                var anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');

                // Workaround to open reviews tab when click on link under product image.
                anchor = anchor === 'review-form' ? 'reviews' : anchor;
                $('[data-role="content"]', me.element).each(function (index) {
                    if (this.id === anchor) {
                        me.activate(index);
                        _scrollAnimated($(this));
                        event.preventDefault();
                        event.stopImmediatePropagation();

                        return false;
                    }
                });
            });

            // force my click listener to run first
            // inspired by https://stackoverflow.com/a/13980262
            $(me.options.externalLink).each(function () {
                var handlers;

                handlers = jQuery._data(this).events.click;
                handlers.unshift(handlers.pop());
            });
        },

        /**
         * Listen tab before open.
         */
        _bindBeforeOpen: function () {
            var me = this;

            me.collapsibles.on('beforeOpen', function (event) {
                var currentTab = $(event.currentTarget),
                    height;

                height = me.getContent(me.lastOpened).outerHeight();
                me.lastOpened = $(event.currentTarget);

                if (me.isAjaxTab(currentTab) &&
                    $(window).width() > 767
                ) {
                    // Tab has ajax content. Set height for the tab content to
                    // reduce jumps of page content.
                    me.getContent(currentTab).css('height', height);
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
            var me = this;

            return me.collapsibles.filter(function () {
                return $(this).hasClass(me.options.openedState);
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
