<?php
namespace Swissup\Easytabs\Block\Adminhtml\Widget\Form\Renderer\Fieldset\Element;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Editor extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }
    /**
     * Prepare wysiwyg element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        /* @var $fieldset \Magento\Framework\Data\Form\Element\Fieldset */
        $fieldset = $element->getForm()->getElement($this->getFieldsetId());

        $widgetValues = $this->getLayout()->getBlock('easytabs.tab.options')->getWidgetValues();
        $fieldset->addField(
            'widget_content',
            'editor',
            [
                'name' => 'parameters[widget_content]',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:36em',
                'required' => true,
                'config' => $this->wysiwygConfig->getConfig(),
                'value' => $widgetValues['widget_content'] ?? '',
                'data-form-part' => $this->getData('data-form-part') ?: null
            ]
        );

        return $element;
    }
}
