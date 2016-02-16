<?php
namespace Swissup\Easytabs\Block\Adminhtml\Index;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    /**
     * Json encoder
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Initialize tab edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'tab_id';
        $this->_blockGroup = 'Swissup_Easytabs';
        $this->_controller = 'adminhtml_index';

        parent::_construct();

        if ($this->_isAllowedAction('Swissup_Easytabs::save')) {
            $this->buttonList->update('save', 'label', __('Save Tab'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Swissup_Easytabs::delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Tab'));
        } else {
            $this->buttonList->remove('delete');
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('widget_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'widget_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'widget_content');
                }
            }
        ";
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('easytab')->getTabId()) {
            return __("Edit Tab '%1'", $this->escapeHtml($this->coreRegistry->registry('easytab')->getTitle()));
        } else {
            return __('New Tab');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }

    /**
     * Prepare Layout Content
     */
    protected function _prepareLayout()
    {
        $model  = $this->coreRegistry->registry('easytab');
        $values = $model->getData();
        $values = isset($values['tab_id']) ? $this->jsonEncoder->encode($values) : 'false';
        $this->_formScripts[] = "
            require([
                'jquery',
                'tabOptions'
            ], function ($, tabOptions) {
                tabOptions.init('" . $this->getUrl("easytabs/*/loadOptions") . "', " . $values . ");
            });
        ";
        return parent::_prepareLayout();
    }
}
