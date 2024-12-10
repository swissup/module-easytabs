define([
    'jquery',
    'mage/accordion',
    'mage/translate',
    'Magento_Ui/js/lib/view/utils/async'
], function ($, accordion) {
    'use strict';

    const isBreeze = !!$.breezemap;
    const buttonsTmpl =
        '<button name="prev" style="display: none" tabindex="-1" aria-hidden="true">' +
            '<span>' +
                $.mage.__('Previous') +
            '</span>' +
        '</button>' +
        '<button name="next" style="display: none" tabindex="-1" aria-hidden="true">' +
            '<span>' +
                $.mage.__('Next') +
            '</span>' +
        '</button>';

    const forceLoadReviews = (tabId) => {
        const $tab = $('#' + tabId.replace('.', '\\.'));
        const role = $tab.attr('role');

        $tab.attr('role', 'tab');
        $tab.collapsible('option', 'content')
            .one('contentUpdated', () => { $tab.attr('role', role); });
        $tab.trigger('beforeOpen');
    }

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
            const anchor = decodeURI(window.location.hash.replace('#', ''));

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
            me.$tablist = me.element.find('[role=tablist]');
            if (me.$tablist.length) {
                let resizeCb = me._onTablistResize.bind(me);

                me.element.append(buttonsTmpl);
                resizeCb();
                me.tablistResizeObserver = new ResizeObserver(resizeCb);
                me.tablistResizeObserver.observe(me.$tablist.get(0));
                me.$tablist.on('scroll.easytabs', resizeCb);
                me._on({'click [name=next],[name=prev]': '_onTablistScrollButtonClick'});
            }
            if (anchor)
                me.activateById(anchor);
        },

        _updateARIA: function () {
            const me = this;

            if (me.options.multipleCollapsible && !me.options.collapsible) {
                // expanded layout (non collapsible accordion)
                // accessability tweaks
                // pattern described at https://www.w3.org/WAI/ARIA/apg/patterns/accordion/
                me.headers
                    .attr('role', 'heading')
                    .attr('aria-level', '2')
                    .attr('aria-disabled', true)
                    .removeAttr('aria-selected')
                    .removeAttr('tabindex');
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

                // Fix for reviews tab. Reviews list loads via ajax.
                if (content.find('#product-review-container:empty').length)
                    forceLoadReviews(content.attr('aria-labelledby'));
            });

            // triggered in breeze only
            me._on('collapsible:afterLoad', (event) => {
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

                if (me.activateById(anchor)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                };
            });

            // force my click listener to run first
            // inspired by https://stackoverflow.com/a/13980262
            $(me.options.externalLink).each(function () {
                var handlers = jQuery._data(this).events?.click;

                if (handlers) {
                    handlers.unshift(handlers.pop());
                }
            });
        },

        /**
         * Listen tab before open.
         */
        _bindBeforeOpen: function () {
            const reducePageJumps = (event) => {
                const $tab = $(event.target);
                const $content = $tab.collapsible('option', 'content');
                const height = this.$lastOpened.collapsible('option', 'content').outerHeight();

                this.$lastOpened = $tab;

                if (this.isAjaxTab($tab) && $(window).width() > 767) {
                    // Tab has ajax content. Set height for the tab content to
                    // reduce jumps of page content.
                    $content.css('height', height);
                }
            }

            this._on({
                'beforeOpen [data-role=collapsible]': reducePageJumps,
                'collapsible:beforeOpen [data-role=collapsible]': reducePageJumps
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
         * @param  {String}  id               Can be tab or any child of content
         * @param  {Boolean} noScrollIntoView
         * @return {Boolean}
         */
        activateById: function (id, noScrollIntoView) {
            var isActivated = false;

            if (!isNaN(id[0])) {
                return isActivated;
            }

            this.collapsibles
                .each((i, tab) => {
                    const $content = $(tab).collapsible('option', 'content');
                    var $toActivate = $content;

                    if ($content.attr('id') !== id) {
                        try {
                            $toActivate = $content.find('#' + id.replace('.', '\\.'));
                        } catch (e) {
                            return; // hash contains chars that are not supported by query selector.
                        }
                    }

                    const scrollToElement = () => {
                        window.scrollTo({
                            left: 0,
                            top: $toActivate.offset().top - this._calculateScrollOffset(),
                            behavior: 'smooth'
                        });
                    };

                    if ($toActivate.length) {
                        if (!noScrollIntoView)
                            $(tab).one('dimensionsChanged', scrollToElement);

                        // Fix for reviews tab. Reviews list loads via ajax.
                        if ($content.find('#product-review-container:empty').length)
                            $content.one('contentUpdated', scrollToElement);

                        $(tab).collapsible(isBreeze ? 'open' : 'forceActivate');
                        isActivated = true;
                    }
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
        },

        _onTablistResize: function () {
            const tablist = this.$tablist.get(0);

            this.element.find('[name=prev]')[tablist.scrollLeft > 2 ? 'show' : 'hide']();
            this.element.find('[name=next]')[tablist.scrollWidth - tablist.offsetWidth - tablist.scrollLeft > 2 ? 'show' : 'hide']();
        },

        _onTablistScrollButtonClick: function (event) {
            const button = event.target;
            const tablist = this.$tablist.get(0);

            event.preventDefault();
            this.collapsibles.each((i, el) => {
                if (el.offsetLeft < tablist.scrollLeft)
                    return true;

                if (button.name == 'next') {
                    tablist.scrollTo({
                        left: el.offsetLeft + el.offsetWidth - button.offsetWidth,
                        behavior: 'smooth'
                    });

                    return false;
                } else if (button.name == 'prev' && el.previousElementSibling) {
                    tablist.scrollTo({
                        left: el.previousElementSibling.offsetLeft - button.offsetWidth,
                        behavior: 'smooth'
                    });

                    return false;
                }
            });
        },

        destroy: function () {
            this.$tablist.off('scroll.easytabs');
            this._super();
        }
    });

    return $.swissup.tabs;
});
