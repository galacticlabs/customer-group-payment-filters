<?php

namespace GalacticLabs\CustomerGroupPaymentFilters\Api;

use GalacticLabs\CustomerGroupPaymentFilters\Api\Data\PaymentFilterInterface;

interface PaymentFilterRepositoryInterface
{
    /**
     * @param int $customerGroupId
     * @return PaymentFilterInterface
     */
    public function getByCustomerGroupId($customerGroupId);

    /**
     * @param PaymentFilterInterface $paymentFilter
     * @return PaymentFilterInterface
     */
    public function save(PaymentFilterInterface $paymentFilter);

    /**
     * @param PaymentFilterInterface $paymentFilter
     * @return void
     */
    public function delete(PaymentFilterInterface $paymentFilter);
}