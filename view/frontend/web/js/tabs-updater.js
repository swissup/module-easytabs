define([
    'jquery',
    'Magento_Ui/js/modal/modal' // 2.3.3: create 'jquery-ui-modules/widget' dependency
], function ($) {
    'use strict';

    $.widget('swissup.tabsUpdater', {
        component: 'Swissup_Easytabs/js/tabs-updater',

        options: {
            aliases: [],
            url: '',
            swatchOptionsSelector: '.product-options-wrapper [data-role=swatch-options]',
            configurableOptionsSelector: '#product_addtocart_form .field.configurable'
        },

        _update: function (productId) {
            const me = this;

            me.options.aliases.forEach((alias) => {
                const $tabTitle = $(document.getElementById(`tab-label-${alias}-title`));
                const $tabItemTitle = $tabTitle.closest('.item.title');

                var url = me.options.url.replace('/tab_alias/', `/${alias}/`);

                if (productId)
                    url = url.replace('/id/', '/parent_id/') + `id/${productId}/`;

                $tabTitle.attr('data-ajaxurl', url);
                $tabItemTitle.attr('data-loaded', false);
                if ($tabItemTitle.hasClass('active'))
                    me._activateTab(alias);
            });
        },

        _create: function () {
            const me = this;

            me._super();

            // listen swatch option change
            $(me.options.swatchOptionsSelector).on('change.tabsupdater', (event) => {
                const $swatches = $(event.currentTarget);
                const swatchRenderer = $swatches.data('mageSwatchRenderer') || $swatches.data('mage-SwatchRenderer');

                if (swatchRenderer)
                    me._update(swatchRenderer.getProduct());
            });

            // listen configurable option change
            $(me.options.configurableOptionsSelector).on('change.tabsupdater', (event) => {
                const $productForm = $('#product_addtocart_form');
                const configurable = $productForm.data('mageConfigurable');

                if (configurable)
                    me._update(configurable.simpleProduct);
            });
        },

        _activateTab: function (alias) {
            const $tabs = $(this.element);

            var tabsWidget = $tabs.data('swissupTabs');

            if (!tabsWidget)
                // No instance. Then it can be Breeze-based frontend
                tabsWidget = $tabs.tabs('instance');


            $tabs.find('[data-role="content"]').each((index, content) => {
                if ($(content).attr('id') === alias) {
                    if (typeof tabsWidget.activate === 'function')
                        tabsWidget.activate(index);
                    else
                        tabsWidget.collapsibles.eq(index).collapsible('open');
                }
            });
        },

        _destroy: function () {
            $(me.options.swatchOptionsSelector).off('change.tabsupdater');
            $(me.options.configurableOptionsSelector).off('change.tabsupdater');
        }
    });

    return $.swissup.tabsUpdater;
});
