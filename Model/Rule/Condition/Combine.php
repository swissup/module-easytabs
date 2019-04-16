<?php

namespace Swissup\Easytabs\Model\Rule\Condition;

use Swissup\SeoTemplates\Model\Template;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CatalogRule\Model\Rule\Condition\ProductFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CatalogRule\Model\Rule\Condition\ProductFactory $conditionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->productFactory = $conditionFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
        $this->setType(\Swissup\Easytabs\Model\Rule\Condition\Combine::class);
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
                $conditions,
                [
                    [
                        'value' => \Swissup\Easytabs\Model\Rule\Condition\Combine::class,
                        'label' => __('Conditions Combination'),
                    ]
                ]
            );

        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'label' => __('Customer'),
                    'value' => [
                        [
                            'label' => __('Customer Group'),
                            'value' => 'Swissup\Easytabs\Model\Rule\Condition\Customer|group_id'
                        ]
                    ]
                ]
            ]
        );

        $productAttributes = $this->productFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($productAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Swissup\Easytabs\Model\Rule\Condition\Product|' . $code,
                'label' => $label,
            ];
        }

        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'label' => __('Product Attribute'),
                    'value' => $attributes
                ]
            ]
        );

        return $conditions;
    }

    /**
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
