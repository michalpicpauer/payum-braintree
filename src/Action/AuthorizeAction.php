<?php

namespace Payum\Braintree\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Braintree\Request\Api\DoSale;
use Payum\Braintree\Reply\Api\TransactionResultArray;
use Braintree\Transaction;

class AuthorizeAction extends AbstractSaleAction
{
    /**
     * {@inheritDoc}
     *
     * @param Authorize $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if ($details->offsetExists('status')) {
            return;
        }

        $details['saleOptions'] = [
            'submitForSettlement' => false
        ];


        $saleRequest = new DoSale($details);
        $this->gateway->execute($saleRequest);

        $transaction = $saleRequest->getResponse();

        $details['sale'] = TransactionResultArray::toArray($transaction);

        $this->resolveStatus($details);

        $details->validateNotEmpty([
            'paymentMethodNonce',
            'paymentMethodNonceInfo',
            'sale',
            'status'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Authorize && $request->getModel() instanceof \ArrayAccess;
    }
}
