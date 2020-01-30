<?php

namespace Payum\Braintree;

use Http\Message\MessageFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Braintree\Configuration;
use Braintree\ClientToken;
use Braintree\PaymentMethodNonce;
use Braintree\Transaction;
use Payum\Core\HttpClientInterface;

class Api
{
    /**
     * @var ArrayObject
     */
    protected $options = [];

    public function __construct(ArrayObject $options)
    {
        $this->options = $options;

        Configuration::reset();

        $environment = 'sandbox';

        if (isset($this->options['environment'])) {
            $environment = $this->options['environment'];
        } elseif (isset($this->options['sandbox'])) {
            $environment = !$this->options['sandbox'] ? 'production' : 'sandbox';
        }

        Configuration::environment($environment);
        Configuration::merchantId($this->options['merchantId']);
        Configuration::publicKey($this->options['publicKey']);
        Configuration::privateKey($this->options['privateKey']);
    }

    /**
     * Generates client token.
     *
     * @param array $params
     * @return string
     */
    public function generateClientToken(array $params = [])
    {
        return ClientToken::generate($params);
    }

    /**
     * @param string $nonceString
     * @return PaymentMethodNonce
     */
    public function findPaymentMethodNonce($nonceString)
    {
        return PaymentMethodNonce::find($nonceString);
    }

    public function sale(ArrayObject $params)
    {
        $options = $params->offsetExists('options') ? $params['options'] : [];

        $params['options'] = $options;

        return Transaction::sale((array)$params);
    }

}
