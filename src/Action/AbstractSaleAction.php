<?php

namespace Payum\Braintree\Action;

use Braintree\Transaction;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;

abstract class AbstractSaleAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    protected function resolveStatus(ArrayObject $details)
    {
        $details->validateNotEmpty(['sale']);

        $sale = $details['sale'];

        if (true === $sale['success']) {

            switch ($sale['transaction']['status']) {

                case Transaction::AUTHORIZED:
                case Transaction::AUTHORIZING:

                    $details['status'] = 'authorized';
                    break;

                case Transaction::SUBMITTED_FOR_SETTLEMENT:
                case Transaction::SETTLING:
                case Transaction::SETTLED:
                case Transaction::SETTLEMENT_PENDING:
                case Transaction::SETTLEMENT_CONFIRMED:

                    $details['status'] = 'captured';
                    break;
            }
        } else {

            $details['status'] = 'failed';
        }
    }
}
