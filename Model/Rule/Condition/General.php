<?php

namespace Swissup\Easytabs\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;
use Magento\Framework\App\ObjectManager;

class General extends \Magento\Rule\Model\Condition\AbstractCondition
{
    const AMP_FLAG = 'swissup_amp_flag';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param Context                           $context
     * @param array                             $data
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        Context $context,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $data);
    }
    /**
     * {@inheritdoc}
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() == self::AMP_FLAG) {
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
        }

        return parent::getValueSelectOptions();

    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeOptions()
    {
        $options = [self::AMP_FLAG => 'Swissup AMP'];
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
