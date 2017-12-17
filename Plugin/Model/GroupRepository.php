<?php

namespace GalacticLabs\CustomerGroupPaymentFilters\Plugin\Model;

use GalacticLabs\CustomerGroupPaymentFilters\Api\PaymentFilterRepositoryInterface;
use GalacticLabs\CustomerGroupPaymentFilters\Api\Data\PaymentFilterInterfaceFactory;
use Magento\Customer\Api\Data\GroupExtensionFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\App\RequestInterface;

class GroupRepository
{
    /**
     * @var PaymentFilterRepositoryInterface
     */
    private $paymentFilterRepository;
    /**
     * @var GroupExtensionFactory
     */
    private $extensionFactory;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var PaymentFilterInterfaceFactory
     */
    private $filterInterfaceFactory;

    public function __construct(
        RequestInterface $request,
        PaymentFilterRepositoryInterface $paymentFilterRepository,
        GroupExtensionFactory $extensionFactory,
        PaymentFilterInterfaceFactory $filterInterfaceFactory
    )
    {
        $this->request = $request;
        $this->paymentFilterRepository = $paymentFilterRepository;
        $this->extensionFactory = $extensionFactory;
        $this->filterInterfaceFactory = $filterInterfaceFactory;
    }

    /**
     * Here we hook into the repository getById method in order to add our new attribute
     * to the returned data.
     *
     * @param GroupRepositoryInterface $subject
     * @param \Magento\Customer\Api\Data\GroupInterface $customerGroup
     * @return \Magento\Customer\Api\Data\GroupInterface
     */
    public function afterGetById(GroupRepositoryInterface $subject, \Magento\Customer\Api\Data\GroupInterface $customerGroup)
    {
        $disallowedPaymentOptions = $this->paymentFilterRepository->getByCustomerGroupId($customerGroup->getId());

        $extensionAttributes = $customerGroup->getExtensionAttributes();
        if($extensionAttributes == null){
            $extensionAttributes = $this->extensionFactory->create();
        }

        $extensionAttributes->setDisallowedPaymentOptions($disallowedPaymentOptions);
        $customerGroup->setExtensionAttributes($extensionAttributes);

        return $customerGroup;
    }

    /**
     * After repo save we'll try to save our disallowed payment methods if
     * we set any.
     *
     * @param GroupRepositoryInterface $subject
     * @param \Magento\Customer\Api\Data\GroupInterface $customerGroup
     * @return \Magento\Customer\Api\Data\GroupInterface
     */
    public function afterSave(GroupRepositoryInterface $subject, \Magento\Customer\Api\Data\GroupInterface $customerGroup){

        try {
            $disallowedPaymentOptions = $this->request->getParam('disallowed_payment_options');

            if($disallowedPaymentOptions == null){
                $disallowedPaymentOptions = [];
            }

            $paymentFilter = $this->filterInterfaceFactory->create();
            $paymentFilter->setCustomerGroupId($customerGroup->getId());
            $paymentFilter->setDisallowedPaymentOptions($disallowedPaymentOptions);

            $this->paymentFilterRepository->save($paymentFilter);
        } catch (\Exception $e) { /** TODO: Do something with the exception */}

        return $customerGroup;
    }

    /**
     * Delete the payment filter before the customer group is deleted. We do this here
     * so we know which customer group is being deleted. Unfortunately the deletion
     * of a customer group returns a bool so we can't do it as an after plugin.
     *
     * @param GroupRepositoryInterface $subject
     * @param $id
     */
    public function beforeDeleteById(GroupRepositoryInterface $subject, $id){
        $paymentFilter = $this->paymentFilterRepository->getByCustomerGroupId($id);

        if($paymentFilter->getCustomerGroupId() != null){
            $this->paymentFilterRepository->delete($paymentFilter);
        }
    }

}