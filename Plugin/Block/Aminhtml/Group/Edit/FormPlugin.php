<?php

namespace GalacticLabs\CustomerGroupPaymentFilters\Plugin\Block\Aminhtml\Group\Edit;

use Closure;
use Magento\Framework\Registry;
use Magento\Customer\Block\Adminhtml\Group\Edit\Form as CustomerGroupForm;
use Magento\Customer\Controller\RegistryConstants;

class FormPlugin
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    private $paymentHelper;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var \Magento\Customer\Api\Data\GroupInterfaceFactory
     */
    private $groupDataFactory;
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Api\Data\GroupInterfaceFactory $groupDataFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
    )
    {
        $this->paymentHelper = $paymentHelper;
        $this->coreRegistry = $coreRegistry;
        $this->groupDataFactory = $groupDataFactory;
        $this->groupRepository = $groupRepository;
    }

    public function aroundGetFormHtml(CustomerGroupForm $subject, Closure $proceed)
    {
        $customerGroup = $this->getCustomerGroup();
        $disabledPaymentMethods = $this->getDisallowedPaymentOptions($customerGroup);
        $form = $subject->getForm();

        if (is_object($form)) {
            $fieldset = $form->addFieldset(
                'disallowed_payment_options_fieldset',
                [
                    'legend' => __('Disallowed Payment Options')
                ]
            );

            $fieldset->addField(
                'disallowed_payment_options_multiselect',
                'multiselect',
                [
                    'name' => 'disallowed_payment_options[]',
                    'label' => __('Disallowed Payment Options'),
                    'id' => 'disallowed_payment_options',
                    'title' => __('Disallowed Payment Options'),
                    'required' => false,
                    'note' => 'Multi select the payment options that you do NOT want this customer group to be able to use.',
                    'value' => $disabledPaymentMethods,
                    'values' => $this->getPaymentMethodsList()
                ]
            );

            $subject->setForm($form);
        }

        return $proceed();
    }

    /**
     * Use the payment helper to gather details about payment options available.
     * Specifically we get an array back of key/value pairs of payment code against
     * its description. We then just reformat that so we can use it nicely in the
     * multi-select.
     *
     * @return array
     */
    private function getPaymentMethodsList()
    {
        $paymentOptions = $this->paymentHelper->getPaymentMethodList();

        return array_filter(array_map(function ($paymentMethodCode, $paymentMethodDescription) {
            if($paymentMethodDescription == '') return;

            return array(
                'value' => $paymentMethodCode,
                'label'  => "{$paymentMethodDescription} ({$paymentMethodCode})"
            );
        }, array_keys($paymentOptions), $paymentOptions));
    }

    /**
     * If there is no current customer group ID then we are creating a
     * new customer group.
     *
     * @return \Magento\Customer\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomerGroup()
    {
        $groupId = $this->coreRegistry->registry(RegistryConstants::CURRENT_GROUP_ID);

        $customerGroup = $groupId === null
            ? $this->groupDataFactory->create()
            : $this->groupRepository->getById($groupId);

        return $customerGroup;
    }

    /**
     * Get the disallowed payment options for the group. If its a new
     * group we'll send back an empty array. This will be used to populate
     * the multi select list.
     *
     * @param $customerGroup
     * @return array
     */
    private function getDisallowedPaymentOptions($customerGroup)
    {
        if ($customerGroup->getExtensionAttributes() !== null && $customerGroup->getExtensionAttributes()->getDisallowedPaymentOptions() !== null) {
            $disallowedPaymentMethods = $customerGroup->getExtensionAttributes()
                ->getDisallowedPaymentOptions()
                ->getDisallowedPaymentOptions();
        } else {
            $disallowedPaymentMethods = [];
        }

        return $disallowedPaymentMethods;
    }
}