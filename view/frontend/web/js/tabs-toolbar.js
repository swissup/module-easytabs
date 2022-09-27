define([
    'jquery',
    'Magento_Ui/js/modal/modal' // 2.3.3: create 'jquery-ui-modules/widget' dependency
], function ($) {
    'use strict';

    /**
     * https://gist.github.com/jtsternberg/c272d7de5b967cec2d3d?permalink_comment_id=3637371#gistcomment-3637371
     */
    let isColliding = function (div1, div2) {

        let d1Offset = div1.offset();
        let d1Height = div1.outerHeight(true);
        let d1Width = div1.outerWidth(true);
        let d1Top = d1Offset.top + d1Height;
        let d1Left = d1Offset.left + d1Width;

        let d2Offset = div2.offset();
        let d2Height = div2.outerHeight(true);
        let d2Width = div2.outerWidth(true);
        let d2Top = d2Offset.top + d2Height;
        let d2Left = d2Offset.left + d2Width;

        return !(d1Top < d2Offset.top || d1Offset.top > d2Top || d1Left < d2Offset.left || d1Offset.left > d2Left);
    };

    $.widget('swissup.tabsToolbar', {
        component: 'Swissup_Easytabs/js/tabs-toolbar',

        _create: function () {
            const me = this;

            me._super();
            me.activate(me._findActiveTab()?.attr('id'));
            $(window).on('scroll.easytabstoolbar', me._scrollListener.bind(me));
        },

        _scrollListener: function () {
            const me = this;

            me.deactivateAll();
            me.activate(me._findActiveTab()?.attr('id'));
        },


        _findActiveTab: function () {
            const $toolbar = $(this.element);

            var $active;

            $(this.element)
                .siblings()
                .find('[data-role="content"]')
                .each(function () {
                    const $content = $(this);

                    if (isColliding($toolbar, $content))
                        $active = $content;
                });

            return $active;
        },

        deactivateAll: function () {
            $(this.element).find('[href]').removeClass('active');
        },

        activate: function (tabId) {
            $(this.element).find(`[href="#${tabId}"]`).addClass('active');
        },

        _destroy: function () {
            $(window).off('scroll.easytabstoolbar');
        }
    });

    return $.swissup.tabsToolbar;
});
