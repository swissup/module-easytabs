/* global FORM_KEY */
define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    var self,
        url,
        formValues;

    return {
        /**
         * @param  {String} ajaxCallUrl
         * @param  {Object} values
         */
        init: function (ajaxCallUrl, values) {
            self = this;
            url = ajaxCallUrl;
            formValues = values;

            $('#easytab_block_type').on('change', function () {
                var value = $(this).val();

                self.load(value);
                $('#easytab_block').val(value);
            });
            self.load($('#easytab_block_type').val());
        },

        /**
         * Load widget options
         *
         * @param  {String} type
         */
        load: function (type) {
            var params = {
                'widget_type': type
            };

            if (formValues && formValues.block === type) {
                params.values = formValues;
            }

            $.ajax({
                method: 'POST',
                url: url,
                showLoader: true,
                data: {
                    widget: JSON.stringify(params),
                    isAjax: 'true',
                    'form_key': FORM_KEY
                }
            })
            .done(self.insertHtml)
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__('An error occured:') + errorThrown
                });
            });
        },

        /**
         * @param  {String} html
         */
        insertHtml: function (html) {
            if ($('.entry-edit.form-inline').length > 1) {
                $('.entry-edit.form-inline').last().remove();
            }
            $('#easytab_base_fieldset').after(html);
            self.showWidgetDescription();
        },

        /**
         * Show widget description
         */
        showWidgetDescription: function () {
            var widgetEl = $('#easytab_block_type'),
                noteCnt = widgetEl.parent().find('small'),
                descrCnt = $('#widget-description-' + (widgetEl.prop('selectedIndex') + 1)),
                description = descrCnt !== undefined ? descrCnt.html() : '';

            if (noteCnt !== undefined) {
                noteCnt.html(description);
            }
        }
    };
});
