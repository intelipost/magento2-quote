/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../model/shipping-rates-validator/intelipost',
    '../model/shipping-rates-validation-rules/intelipost'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    intelipostShippingRatesValidator,
    intelipostShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('intelipost', intelipostShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('intelipost', intelipostShippingRatesValidationRules);

    return Component;
});