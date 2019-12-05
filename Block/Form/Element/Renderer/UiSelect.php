<?php

namespace Swissup\Easytabs\Block\Form\Element\Renderer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;

class UiSelect extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'ui-select.phtml';

    /**
     * Initialize js layout.
     *
     * @param  AbstractElement $element
     */
    public function initJsLayout(AbstractElement $element)
    {
        $value = $element->getValue();
        $this->jsLayout = [
            'components' => [
                $this->getScopeName() => [
                    'component' => 'uiComponent',
                    'children' => [
                        $element->getName() => [
                            'type' => 'form.select',
                            'name' => $element->getName(),
                            'value' => is_array($value) ? $value : [],
                            'config' => [
                                'component' => 'Swissup_Easytabs/js/form/element/ui-select',
                                'template' => 'Swissup_Easytabs/form/element/ui-select/old-php-form-workaround',
                                'dataType' => 'text',
                                'visible' => true,
                                'formElement' => 'select',
                                'formPart' => $this->getData('data-form-part'),
                                'options' => $this->getOptions()
                            ]
                        ]
                    ]

                ]
            ]
        ];
    }

    /**
     * Prepare UI Select element HTML
     *
     * @param  AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $this->initJsLayout($element);
        $element->setData('after_element_html', $this->toHtml());
        return $element;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if ($sourceModel = $this->getSourceModel()) {
            return $sourceModel->toOptionArray();
        }

        return [];
    }

    /**
     * @return object
     */
    public function getSourceModel()
    {
        if (!$sourceModel = $this->getData('source_model')) {
            return null;
        }

        if (!is_object($sourceModel)) {
            $objectManager = ObjectManager::getInstance();
            $sourceModel = $objectManager->get($sourceModel);
            $this->setData('source_model', $sourceModel);
        }

        return $sourceModel;
    }
}
