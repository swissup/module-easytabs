(function () {
    'use strict';

    $.widget('easytabs', {
        component: 'Swissup_Easytabs/js/tabs',
        options: {
            ajaxUrlElement: '[data-ajaxurl]',
            ajaxUrlAttribute: 'data-ajaxurl'
        },

        /** [create description] */
        create: function () {
            this.element.tabs(this.options);
        }
    });
})();
