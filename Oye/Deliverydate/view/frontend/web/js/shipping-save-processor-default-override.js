console.log('init Oye/Deliverydate/view/frontend/web/js/shipping-save-processor-default-override.js')
define(
    [
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor'
    ],
    function (ko, quote, resourceUrlManager, storage, paymentService, methodConverter, errorProcessor) {
        'use strict';
        return {
            saveShippingInformation: function() {
                var payload = {

                addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code,
                        extension_attributes: {
                            delivery_date: jQuery('[name="shippingAddress[delivery_date]"]').val()
                        }
                    }
                };

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                );
            }
        };
    }
);
