<?php

namespace Payum\Braintree;

use Payum\Braintree\Action\AuthorizeAction;
use Payum\Braintree\Action\CaptureAction;
use Payum\Braintree\Action\StatusAction;
use Payum\Braintree\Action\Api\GenerateClientTokenAction;
use Payum\Braintree\Action\Api\FindPaymentMethodNonceAction;
use Payum\Braintree\Action\Api\DoSaleAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class BraintreeGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'braintree',
            'payum.factory_title' => 'braintree',
            'payum.template.braintree_dropin' => '@PayumBraintree/Action/braintree_dropin.html.twig',
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.capture' => new CaptureAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.api.generate_client_token' => new GenerateClientTokenAction(),
            'payum.action.api.find_payment_method_nonce' => new FindPaymentMethodNonceAction(),
            'payum.action.api.do_sale' => new DoSaleAction(),
        ]);

        if (false === $config['payum.api']) {
            $config['payum.default_options'] = [
                'sandbox' => true
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api($config);
            };
        }

        $config['payum.paths'] = [
            'PayumBraintree' => __DIR__ . '/Resources/views'
        ];
    }
}
