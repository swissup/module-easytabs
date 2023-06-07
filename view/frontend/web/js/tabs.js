define([
    'jquery',
    'mage/accordion',
    'Magento_Ui/js/lib/view/utils/async'
], function ($, accordion) {
    'use strict';

    const isBreeze = !!$.breezemap;

    if (isBreeze)
        accordion = 'accordion';

    $.widget('swissup.tabs', accordion, {
        component: 'Swissup_Easytabs/js/tabs',
        options: {
            collapsible: false,
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
            const me = this;
            const anchor = window.location.hash.replace('#', '');

            me._bindAfterAjax();
            me._super();
            me.$lastOpened = me.collapsibles
                .filter((i, el) =>
                    $(el).collapsible('option', 'content').attr('aria-hidden') === 'false'
                );
            me.$lastOpened.trigger('beforeOpen');
            me._bindExternalLinks();
            me._bindBeforeOpen();
            me._updateARIA();
            if (anchor)
                me.activateById(anchor);
        },

        /**
         * {@inheritdoc}
         */
        _updateARIA: function () {
            const me = this;

            if (me.options.multipleCollapsible && !me.options.collapsible) {
                // expanded layout (non collapsible accordion)
                // accessability tweaks
                // pattern described at https://www.w3.org/WAI/ARIA/apg/patterns/accordion/
                me.headers
                    .attr('role', 'button')
                    .attr('aria-disabled', true)
                    .removeAttr('aria-selected');
                me.contents
                    .attr('role', 'region');
                me.element
                    .removeAttr('role')
                    .on('dimensionsChanged', () => { me.headers.removeAttr('aria-selected') });
            } else if (me.options.collapsible) {
                // accordion layout
                // accessability tweaks
                // pattern described at https://www.w3.org/WAI/ARIA/apg/patterns/accordion/
                me.headers
                    .attr('role', 'button')
                    .removeAttr('aria-selected');
                me.contents
                    .attr('role', 'region');
                me.element
                    .removeAttr('role')
                    .on('dimensionsChanged', () => { me.headers.removeAttr('aria-selected') });
            }
        },

        /**
         * Listen tab content update after Ajax request.
         */
        _bindAfterAjax: function () {
            const me = this;

            $.async({
                selector: '[data-role=content][aria-busy=true] > :first-child',
                ctx: me.element.get(0)
            }, (firstChild) => {
                const content = $(firstChild).parent();

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
                content.find('input[name="form_key"]').val(
                    $.mage.cookies.get('form_key')
                );
            });

            // triggered in breeze only
            me.element.on('collapsible:afterLoad', (event) => {
                const $ajaxElement = $(event.target).find(me.options.ajaxUrlElement);

                if (me.options.ajaxContentOnce)
                    $ajaxElement.removeAttr(me.options.ajaxUrlAttribute);
            });
        },

        /**
         * Listen external link click to activate tab.
         */
        _bindExternalLinks: function () {
            const me = this;

            if (isBreeze) return; // Disable when breeze

            $(me.options.externalLink).on('click', function (event) {
                var anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');

                // Workaround to open reviews tab when click on link under product image.
                anchor = anchor === 'review-form' ? 'reviews' : anchor;
                if (me.activateById(anchor)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                };
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
            const me = this;

            me.element.on('beforeOpen collapsible:beforeOpen', (event) => {
                const $tab = $(event.target);
                const $content = $tab.collapsible('option', 'content');
                const height = me.$lastOpened.collapsible('option', 'content').outerHeight();

                me.$lastOpened = $tab;

                if (me.isAjaxTab($tab) && $(window).width() > 767) {
                    // Tab has ajax content. Set height for the tab content to
                    // reduce jumps of page content.
                    $content.css('height', height);
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
         * Check if tab loads content via ajax.
         *
         * @param  {HTMLElement}  element
         * @return {Boolean}
         */
        isAjaxTab: function (element) {
            return !!$(this.options.ajaxUrlElement, element).length;
        },

        /**
         * @param  {String}  id
         * @param  {Boolean} noScrollIntoView
         * @return {Boolean}
         */
        activateById: function (id, noScrollIntoView) {
            var isActivated = false;

            this.collapsibles
                .filter((i, tab) => $(tab).collapsible('option', 'content').attr('id') == id)
                .each((i, tab) => {
                    const $content = $(tab).collapsible('option', 'content');

                    $(tab).collapsible(isBreeze ? 'open' : 'forceActivate');
                    isActivated = true;
                    if (!noScrollIntoView)
                        window.scrollTo({
                            left: 0,
                            top: $content.offset().top - this._calculateScrollOffset(),
                            behavior: 'smooth'
                        });
                });

            return isActivated;
        },

        /**
         * @return {Number}
         */
        _calculateScrollOffset: function () {
            const $toolbar = $(this.element).siblings('.tabs-toolbar');

            var offset, toolbarCss;

            if ($toolbar.length == 0) return 40;

            toolbarCss = window.getComputedStyle($toolbar.get(0));
            offset = parseFloat(toolbarCss.top);
            offset = isNaN(offset) ? 0 : offset;
            offset += $toolbar.outerHeight() - 2 ; // substruct 2 px to make sure
            // that sticky tabs toolbar overlaps with tab content to activat proper title

            return offset;
        }
    });

    return $.swissup.tabs;
});
