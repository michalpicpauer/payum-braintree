<?php

namespace Payum\Braintree\Action;

use Payum\Braintree\Reply\Api\TransactionResultArray;
use Payum\Braintree\Request\Api\DoSale;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Authorize;
use Payum\Core\Request\Capture;

class CaptureAction extends AbstractSaleAction
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

        $details['options'] = [
            'submitForSettlement' => true
        ];

        $saleRequest = new DoSale($details);
        $this->gateway->execute($saleRequest);

        $transaction = $saleRequest->getResponse();

        $details['sale'] = TransactionResultArray::toArray($transaction);

        $this->resolveStatus($details);

        $details->validateNotEmpty([
            'paymentMethodNonce',
            'sale',
            'status'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return $request instanceof Capture && $request->getModel() instanceof \ArrayAccess;
    }
}
