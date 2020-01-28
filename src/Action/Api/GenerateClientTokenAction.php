<?php
namespace Payum\Braintree\Action\Api;

use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Braintree\Request\Api\GenerateClientToken;

class GenerateClientTokenAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @throws LogicException if the token not set in the instruction.
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $requestParams = [];

        $requestCustomerId = $request->getCustomerId();
        $requestMerchantAccountId = $request->getMerchantAccountId();

        if (null !== $requestCustomerId) {
            $requestParams['customerId'] = $requestCustomerId;
        }

        if (null !== $requestMerchantAccountId) {
            $requestParams['merchantAccountId'] = $requestMerchantAccountId;
        }

        $request->setResponse($this->api->generateClientToken($requestParams));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof GenerateClientToken;
    }
}
