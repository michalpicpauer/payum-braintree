<?php

namespace Payum\Braintree\Action\Api;

use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Braintree\Request\Api\DoSale;

class DoSaleAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @throws LogicException if the token not set in the instruction.
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $requestParams = $this->getSaleRequestParams($request);

        $transactionResult = $this->api->sale($requestParams);

        $request->setResponse($transactionResult);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof DoSale && $request->getModel() instanceof \ArrayAccess;
    }

    private function getSaleRequestParams($request)
    {
        $details = ArrayObject::ensureArrayObject($request->getModel());

        $details->validateNotEmpty(['amount']);

        $requestParams = new ArrayObject();

        $forwardParams = [
            'amount',
            'paymentMethodNonce',
            'paymentMethodToken',
            'creditCard',
            'billing',
            'shipping',
            'customer',
            'orderId',
        ];

        foreach ($forwardParams as $paramName) {
            if ($details->offsetExists($paramName)) {
                $requestParams[$paramName] = $details[$paramName];
            }
        }

        if ($details->offsetExists('options')) {
            $requestParams['options'] = $details['options'];
        }

        return $requestParams;
    }
}
