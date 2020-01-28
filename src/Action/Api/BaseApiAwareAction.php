<?php
namespace Payum\Braintree\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Braintree\Api;

abstract class BaseApiAwareAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait;

    /** @var Api */
    protected $api;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }
}
