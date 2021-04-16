/* global breeze */
(function () {
    'use strict';

    breeze.widget('easytabs', {
        options: {
            ajaxUrlElement: '[data-ajaxurl]',
            ajaxUrlAttribute: 'data-ajaxurl'
        },

        /** [create description] */
        create: function () {
            this.element.tabs(this.options);
        }
    });

    $(document).on('breeze:mount:Swissup_Easytabs/js/tabs', function (event, data) {
        $(data.el).easytabs(data.settings);
    });
})();
