<?php

namespace GalacticLabs\CustomerGroupPaymentFilters\Api\Data;

interface PaymentFilterInterface
{
    /**
     * @return int
     */
    public function getCustomerGroupId();

    /**
     * @param int $customerGroupId
     * @return void
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * @return string[]
     */
    public function getDisallowedPaymentOptions();

    /**
     * @param string[] $paymentOptions
     * @return void
     */
    public function setDisallowedPaymentOptions(array $paymentOptions);
}