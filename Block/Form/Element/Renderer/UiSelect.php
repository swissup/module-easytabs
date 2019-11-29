<?php

namespace Swissup\Easytabs\Block\Form\Element\Renderer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class UiSelect extends Template implements RendererInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'ui-select.phtml';

    /**
     * {@inheritdoc}
     */
    public function _construct() {
        if (!$this->getScopeName()) {
            $this->setScopeName('swissup_easytabs_uiselect');
        }

        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->setOptions($element->getValues());
        $this->initJsLayout($element);
        $css = 'admin__field field';
        if ($element->getRequired()) {
            $css .= ' required-entry _required';
        }

        $html = $element->getNoSpan() === true ? '' : "<div class=\"{$css}\">" . "\n";
        $html .= $element->getLabelHtml();
        $html .= $this->toHtml();
        $html .= $element->getNoSpan() === true ? '' : '</div>' . "\n";

        return $html;
    }

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
                                'formPart' => $element->getData('data-form-part'),
                                'options' => $this->getOptions()
                            ]
                        ]
                    ]

                ]
            ]
        ];
    }
}
