<?php

namespace GalacticLabs\CustomerGroupPaymentFilters\Model;

use GalacticLabs\CustomerGroupPaymentFilters\Api\Data\PaymentFilterInterface;
use GalacticLabs\CustomerGroupPaymentFilters\Api\PaymentFilterRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class PaymentFilterRepository implements PaymentFilterRepositoryInterface
{
    private $paymentFilterFactory;

    public function __construct(
        PaymentFilterFactory $paymentFilterFactory
    )
    {
        $this->paymentFilterFactory = $paymentFilterFactory;
    }

    /**
     * @param int $customerGroupId
     * @return PaymentFilterInterface
     */
    public function getByCustomerGroupId($customerGroupId)
    {
        $paymentFilter = $this->paymentFilterFactory->create();
        $paymentFilter->getResource()->load($paymentFilter, $customerGroupId);

        if($paymentFilter->getId() == null){
            $paymentFilter->setDisallowedPaymentOptions([]);
        }

        return $paymentFilter;
    }

    /**
     * @param PaymentFilterInterface $paymentFilter
     * @return PaymentFilterInterface
     */
    public function save(PaymentFilterInterface $paymentFilter)
    {
        $paymentFilter->getResource()->save($paymentFilter);

        return $paymentFilter;
    }

    /**
     * @param PaymentFilterInterface $paymentFilter
     * @return void
     */
    public function delete(PaymentFilterInterface $paymentFilter)
    {
        $paymentFilter->getResource()->delete($paymentFilter);
    }
}