define([
    'underscore',
    'Magento_Ui/js/form/element/ui-select'
], function (_, UiSelect) {
    'use strict';

    return UiSelect.extend({
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
