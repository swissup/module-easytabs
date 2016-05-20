define(['jquery', 'Magento_Ui/js/modal/alert'], function($, alert) {
    var self,
        url,
        formValues;

    return {
        init: function(ajaxCallUrl, values) {
            self = this;
            url = ajaxCallUrl;
            formValues = values;

            $('#easytab_block_type').on('change', function() {
                var value = $(this).val();
                self.load(value);
                $('#easytab_block').val(value);
            });
            self.load($('#easytab_block_type').val());
        },
        load: function(type) {
            var params = { widget_type: type };
            if (formValues && formValues['block'] == type) {
                params['values'] = formValues;
            }
            $.ajax({
                method: "POST",
                url: url,
                showLoader: true,
                data: {
                    widget: JSON.stringify(params),
                    isAjax: 'true',
                    form_key: FORM_KEY
                }
            })
            .done(self.insertHtml)
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__('An error occured:') + errorThrown
                });
            });
        },
        insertHtml: function(html) {
            if ($('.entry-edit.form-inline').length > 1) {
                $('.entry-edit.form-inline').last().remove();
            }
            $('#easytab_base_fieldset').after(html);
            self.showWidgetDescription();
        },
        showWidgetDescription: function() {
            var widgetEl = $('#easytab_block_type');
            var noteCnt = widgetEl.parent().find('small');
            var descrCnt = $('#widget-description-' + (widgetEl.prop('selectedIndex') + 1));
            if (noteCnt != undefined) {
                var description = (descrCnt != undefined ? descrCnt.html() : '');
                noteCnt.html(description);
            }
        }
    }
});
