<?php

namespace Swissup\Easytabs\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;
use Magento\CatalogRule\Model\Rule\CustomerGroupsOptionsProvider;

class Customer extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * {@inheritdoc}
     */
    protected $_inputType = 'multiselect';

    /**
     * @param CustomerGroupsOptionsProvider $customerGroupsProvider
     * @param Context                       $context
     * @param array                         $data
     */
    public function __construct(
        CustomerGroupsOptionsProvider $customerGroupsProvider,
        Context $context,
        array $data = []
    ) {
        $this->customerGroupsProvider = $customerGroupsProvider;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['multiselect'] = ['()', '!()'];
        }

        return $this->_defaultOperatorInputByType;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() == 'group_id') {
            return 'multiselect';
        }

        return parent::getValueElementType();
    }

    /**
     * {@inheritdoc}
     */
    public function getValueSelectOptions()
    {
        return $this->customerGroupsProvider->toOptionArray();
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeOptions()
    {
        $options = ['group_id' => __('Customer Group')];
        $this->setAttributeOption($options);
        return parent::loadAttributeOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        $element->setStyle('vertical-align: top');
        $element->setSize('5');
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $tab)
    {
        $customer = $tab->getCustomer();
        if (!$customer) {
            return true;
        }

        return parent::validate($tab->getCustomer());
    }

    /**
     * {@inheritdoc}
     *
     * Improve logic when value is '0'.
     */
    public function getValueParsed()
    {
        $valueParsed = parent::getValueParsed();
        if ($valueParsed === '0' && $this->isArrayOperatorType()) {
            $valueParsed = preg_split('#\s*[,;]\s*#', $valueParsed, null, PREG_SPLIT_NO_EMPTY);
        }

        return $valueParsed;
    }
}
