<?php

namespace Payum\Braintree\Action\Api;

use Payum\Braintree\Request\Api\CreateCustomer;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Braintree\Request\Api\DoSale;

class CreateCustomerAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @throws LogicException if the token not set in the instruction.
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $requestParams = $this->getCreateCustomerRequestParams($request);

        $transactionResult = $this->api->createCustomer($requestParams);

        $request->setResponse($transactionResult);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof CreateCustomer && $request->getModel() instanceof \ArrayAccess;
    }

    private function getCreateCustomerRequestParams($request): ArrayObject
    {
        $details = ArrayObject::ensureArrayObject($request->getModel());

        $details->validateNotEmpty(['paymentMethodNonce']);

        $requestParams = new ArrayObject();

        $forwardParams = [
            'paymentMethodNonce',
            'creditCard',
            'firstName',
            'lastName'
        ];

        foreach ($forwardParams as $paramName) {
            if ($details->offsetExists($paramName)) {
                $requestParams[$paramName] = $details[$paramName];
            }
        }

        return $requestParams;
    }
}
