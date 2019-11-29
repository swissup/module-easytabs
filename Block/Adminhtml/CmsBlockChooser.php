<?php

namespace Swissup\Easytabs\Block\Adminhtml;

class CmsBlockChooser extends \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser
{
    /**
     * {@inheritdocs}
     */
    public function prepareElementHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        if ($dataFormPart = $this->getData('data-form-part')) {
            $element->setData('data-form-part', $dataFormPart);
        }

        if (!is_string($element->getValue())) {
            $element->setValue('');
        }

        return parent::prepareElementHtml($element);
    }
}
