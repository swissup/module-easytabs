<?php

namespace Swissup\Easytabs\Block\Form\Element\Renderer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Swissup\Easytabs\Block\Adminhtml\Widget\Template;

class UiComponent extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'ui-component.phtml';

    /**
     * Initialize js layout.
     *
     * @param  AbstractElement $element
     */
    public function initJsLayout(AbstractElement $element)
    {
        $value = $element->getValue();
        $config = $this->getComponentConfig();
        $fieldName = $config['fieldName'] ?? false;

        if ($fieldName) {
            $value = $value[$fieldName] ?? '';
        }

        $this->jsLayout = [
            'components' => [
                $this->getScopeName() => [
                    'component' => 'uiComponent',
                    'children' => [
                        $element->getName() => [
                            'name' => $element->getName(),
                            'value' => $value,
                            'config' => $config
                        ]
                    ]

                ]
            ]
        ];
    }

    protected function getComponentConfig()
    {
        $defaults = [
            'formPart' => $this->getData('data-form-part'),
            'options' => $this->getOptions()
        ];
        $defaults = array_filter($defaults);

        return $this->getData('component_config') + $defaults;
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

        return parent::prepareElementHtml($element);
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
