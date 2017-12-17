<?php

namespace GalacticLabs\CustomerGroupPaymentFilters\Model;

use GalacticLabs\CustomerGroupPaymentFilters\Api\Data\PaymentFilterInterface;
use GalacticLabs\CustomerGroupPaymentFilters\Model\ResourceModel\PaymentFilter as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class PaymentFilter extends AbstractModel implements PaymentFilterInterface
{
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const DISALLOWED_PAYMENT_OPTIONS = 'disallowed_payment_options';

    /**
     * Assign the resource model for persistence.
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->_getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * @param int $customerGroupId
     * @return void
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * @return string[]
     */
    public function getDisallowedPaymentOptions()
    {
        return unserialize(
            $this->_getData(self::DISALLOWED_PAYMENT_OPTIONS)
        );
    }

    /**
     * @param string[] $paymentOptions
     * @return void
     */
    public function setDisallowedPaymentOptions(array $paymentOptions)
    {
        $this->setData(self::DISALLOWED_PAYMENT_OPTIONS, serialize($paymentOptions));
    }
}