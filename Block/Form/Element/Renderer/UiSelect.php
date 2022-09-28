<?php

namespace Swissup\Easytabs\Block\Form\Element\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;

class UiSelect extends UiComponent
{
    /**
     * {@inheritdoc}
     */
    public function initJsLayout(AbstractElement $element)
    {
        $value = $element->getValue();
        $config = $this->getComponentConfig();
        $fieldName = $config['fieldName'] ?? false;

        if ($fieldName) {
            $value = $value[$fieldName] ?? [];
        }

        if (!$value) {
            $value = [];
        }

        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        $element->setValue($value);

        return parent::initJsLayout($element);
    }
}
