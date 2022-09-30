define([
    'jquery',
    'Magento_Ui/js/modal/modal' // 2.3.3: create 'jquery-ui-modules/widget' dependency
], function ($) {
    'use strict';

    $.widget('swissup.tabsUpdater', {
        component: 'Swissup_Easytabs/js/tabs-updater',

        options: {
            aliases: [],
            swatchOptionsSelector: '.product-options-wrapper [data-role=swatch-options]',
            configurableOptionsSelector: '#product_addtocart_form .field.configurable'
        },

        _update: function (productId, parentId) {
            const me = this;
            const $el = $(me.element);

            me.options.aliases.forEach((alias) => {
                const $tabTitle = $(document.getElementById(`tab-label-${alias}-title`));
                const url = `${BASE_URL}easytabs/index/index/id/${productId}/parent_id/${parentId}/tab/${alias}`;

                $tabTitle.attr('data-ajaxurl', url);
                if ($tabTitle.closest('.item.title').hasClass('active'))
                    $el.find('[data-role="content"]').each((index, content) => {
                        if ($(content).attr('id') === alias) {
                            $el.tabs('activate', index);
                        }
                    });
            });
        },

        _create: function () {
            const me = this;

            me._super();

            // listen swatch option change
            $(me.options.swatchOptionsSelector).on('change.tabsupdater', (event) => {
                const $swatches = $(event.currentTarget);
                const swatchRenderer = $swatches.data('mageSwatchRenderer') || $swatches.data('mage-SwatchRenderer');

                var productId, parentId;

                if (swatchRenderer) {
                    parentId = swatchRenderer.options.jsonConfig.productId;
                    productId = swatchRenderer.getProduct() || parentId;
                    me._update(productId, parentId);
                }
            });

            // listen configurable option change
            $(me.options.configurableOptionsSelector).on('change.tabsupdater', (event) => {
                const $productForm = $('#product_addtocart_form');
                const configurable = $productForm.data('mageConfigurable');

                if (configurable) {
                    parentId = configurable.options.spConfig.productId;
                    productId = configurable.simpleProduct || parentId;
                    me._update(productId, parentId);
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
