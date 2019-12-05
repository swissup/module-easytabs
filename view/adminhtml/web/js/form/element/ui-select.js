define([
    'underscore',
    'Magento_Ui/js/form/element/ui-select'
], function (_, UiSelect) {
    'use strict';

    return UiSelect.extend({

        /**
         * {@inheritdoc}
         *
         * Clean up initial value from not existing values.
         */
        normalizeData: function (value) {
            var that = this;

            value = _.filter(value, function (optionValue) {
                var items = _.where(that.options(), {
                        value: optionValue
                    });

                return !!items.length;
            });

            return this._super(value);
        },

        /**
         * {@inheritdoc}
         *
         * Rearrange selected values. Last selected at the end.
         */
        getSelected: function () {
            var selectedOptions = this._super(),
                selected = this.value();

            if (!_.isArray(selected)) {
                return selectedOptions;
            }

            return _.map(selected, function (item) {
                    return _.findWhere(this, {
                        value: item
                    });
                }, selectedOptions);
        }
    });
});
