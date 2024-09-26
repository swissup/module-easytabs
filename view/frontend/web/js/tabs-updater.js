define([
    'jquery',
    'Magento_Ui/js/modal/modal' // 2.3.3: create 'jquery-ui-modules/widget' dependency
], function ($) {
    'use strict';

    const _getUiWidgetInstance = ($el, widget) => {
        try {
            return $el[widget]('instance');
        } catch (error) {
            return $el.data(`mage-${widget}`) || $el.data(`mage-${widget}`);
        }
    }

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
                    me._updateTab(alias);
            });
        },

        _create: function () {
            const me = this;

            me._super();

            // listen swatch option change
            $(me.options.swatchOptionsSelector).on('change.tabsupdater', (event) => {
                const $swatches = $(event.currentTarget);
                const swatchRenderer = _getUiWidgetInstance($swatches, 'SwatchRenderer');

                if (swatchRenderer)
                    me._update(swatchRenderer.getProduct());
            });

            // listen configurable option change
            $(me.options.configurableOptionsSelector).on('change.tabsupdater', (event) => {
                const $productForm = $('#product_addtocart_form');
                const configurable = _getUiWidgetInstance($productForm, 'configurable');

                if (configurable)
                    me._update(configurable.simpleProduct);
            });
        },

        _updateTab: function (alias) {
            const $tabs = $(this.element);

            var tabsWidget = $tabs.data('swissupTabs');

            $tabs.find('[data-role="content"]').each((index, content) => {
                if ($(content).attr('id') === alias) {
                    var collapsible = tabsWidget.collapsibles.eq(index).data('mageCollapsible');

                    if (collapsible._loadContent) {
                        collapsible._loadContent();
                    } else if (collapsible.loadContent) {
                        collapsible.loadContent();
                    }
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
