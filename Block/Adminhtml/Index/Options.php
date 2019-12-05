<?php
namespace Swissup\Easytabs\Block\Adminhtml\Index;

class Options extends \Magento\Widget\Block\Adminhtml\Widget\Options
{
    /**
     * @var \Swissup\Easytabs\Model\TabsFactory
     */
    protected $tabsOptionsFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Option\ArrayPool $sourceModelPool
     * @param \Magento\Widget\Model\Widget $widget
     * @param \Swissup\Easytabs\Model\TabsFactory $tabsOptionsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Option\ArrayPool $sourceModelPool,
        \Magento\Widget\Model\Widget $widget,
        \Swissup\Easytabs\Model\TabsFactory $tabsOptionsFactory,
        array $data = []
    ) {
        $this->tabsOptionsFactory = $tabsOptionsFactory;
        parent::__construct($context, $registry, $formFactory, $sourceModelPool, $widget, $data);
    }
    /**
     * Add fields to main fieldset based on specified tab type
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function addFields()
    {
        // get configuration node and translation helper
        if (!$this->getWidgetType()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please specify a Widget Type.'));
        }

        $config = $this->tabsOptionsFactory->create()->getConfigAsObject($this->getWidgetType());
        if (!$config->getParameters()) {
            return $this;
        }

        foreach ($config->getParameters() as $parameter) {
            if ($parameter->getHelperBlock()) {
                $parameter->getHelperBlock()->setData('data-form-part', $this->getFormName());
            }

            $field = $this->_addField($parameter);
            $field->setData('data-form-part', $this->getFormName());
        }

        return $this;
    }

    public function getFormHtml()
    {
        $form = $this->getForm();
        $fieldset = $this->getMainFieldset();

        return $fieldset->getChildrenHtml();
    }
}
