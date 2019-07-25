<?php

namespace Swissup\Easytabs\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;
use Magento\Framework\App\ObjectManager;

class General extends \Magento\Rule\Model\Condition\AbstractCondition
{
    const AMP_FLAG = 'swissup_amp_flag';

    const PRODUCT_TYPE = 'product_type';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    private $productType;

    /**
     * @param \Magento\Framework\Module\Manager   $moduleManager
     * @param \Magento\Catalog\Model\Product\Type $productType
     * @param Context                             $context
     * @param array                               $data
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Product\Type $productType,
        Context $context,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->productType = $productType;
        parent::__construct($context, $data);
    }
    /**
     * {@inheritdoc}
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() == self::AMP_FLAG
            || $this->getAttribute() == self::PRODUCT_TYPE
        ) {
            return 'select';
        }

        return parent::getValueElementType();
    }

    /**
     * {@inheritdoc}
     */
    public function getValueSelectOptions()
    {
        if ($this->getAttribute() == self::AMP_FLAG) {
            return [
                [
                    'value' => '1',
                    'label' => __('Enabled')
                ],
                [
                    'value' => '0',
                    'label' => __('Disabled')
                ]
            ];
        } elseif ($this->getAttribute() == self::PRODUCT_TYPE) {
            return $this->productType->toOptionArray();
        }

        return parent::getValueSelectOptions();

    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeOptions()
    {
        $options = [
            self::PRODUCT_TYPE => __('Product Type'),
            self::AMP_FLAG => __('Swissup AMP')
        ];
        $this->setAttributeOption($options);
        return parent::loadAttributeOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $tab)
    {
        if ($this->getAttribute() == self::AMP_FLAG) {
            $attributeValue = '0';
            if ($this->moduleManager->isOutputEnabled('Swissup_Amp')) {
                $helperAmp = ObjectManager::getInstance()->get(
                    '\Swissup\Amp\Helper\Data'
                );
                if ($helperAmp->canUseAmp()) {
                    $attributeValue = '1';
                }
            }

            return $this->validateAttribute($attributeValue);
        } elseif ($this->getAttribute() == self::PRODUCT_TYPE) {
            $attributeValue = null;
            if ($product = $tab->getProduct()) {
                $attributeValue = $product->getTypeId();
            }

            return $this->validateAttribute($attributeValue);
        }

        return true;
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

    /**
     * {@inheritdoc}
     */
    public function getInputType()
    {
        if ($this->getAttribute() == self::AMP_FLAG) {
            return 'select';
        }

        return parent::getInputType();
    }
}
