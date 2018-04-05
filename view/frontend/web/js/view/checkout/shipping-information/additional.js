<!--
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */
-->

define([
    'uiComponent'
], function (Component) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Intelipost_Quote/checkout/shipping-information/additional'
        },
        getShippingInformationAdditional: function(){
            var result = 'blah';

            jQuery.ajax({
                url: INTELIPOST_QUOTE_SCHEDULE_STATUS_URL,
                async: false,
                showLoader: true, // enable loader

                success: function(data) {
                    result = data;
                },
            });

            return result;
        },
    });
});

