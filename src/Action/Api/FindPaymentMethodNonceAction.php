<?php
namespace Payum\Braintree\Action\Api;

use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Braintree\Request\Api\FindPaymentMethodNonce;
use Braintree\Exception\NotFound;


class FindPaymentMethodNonceAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @throws LogicException if the token not set in the instruction.
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        try {
            $paymentMethodNonce = $this->api->findPaymentMethodNonce($request->getNonceString());

            $request->setResponse($paymentMethodNonce);
        }
        catch(NotFound $exception) {
            return;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof FindPaymentMethodNonce;
    }
}
