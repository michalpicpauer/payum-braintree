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
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;

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
        if (isset($this->options['merchantAccountId'])) {
            $params['merchantAccountId'] = $this->options['merchantAccountId'];
        }

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

        if (null !== $this->options['storeInVault'] && !isset($options['storeInVault'])) {
            $options['storeInVault'] = $this->options['storeInVault'];
        }

        if (null !== $this->options['storeInVaultOnSuccess'] && !isset($options['storeInVaultOnSuccess'])) {
            $options['storeInVaultOnSuccess'] = $this->options['storeInVaultOnSuccess'];
        }

        if (null !== $this->options['addBillingAddressToPaymentMethod'] &&
            !isset($options['addBillingAddressToPaymentMethod']) &&
            $params->offsetExists('billing')) {

            $options['addBillingAddressToPaymentMethod'] = $this->options['addBillingAddressToPaymentMethod'];
        }

        if (null !== $this->options['storeShippingAddressInVault'] &&
            !isset($options['storeShippingAddressInVault']) &&
            $params->offsetExists('shipping')) {

            $options['storeShippingAddressInVault'] = $this->options['storeShippingAddressInVault'];
        }

        $params['options'] = $options;

        if (array_key_exists('merchantAccountId', $this->options) && null !== $this->options['merchantAccountId']) {
            $params['merchantAccountId'] = $this->options['merchantAccountId'];
        }

        return Transaction::sale((array)$params);
    }

}
