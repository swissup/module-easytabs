<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

class LoadOptions extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_save';

    /**
     * Ajax responder for loading plugin options form
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_view->loadLayout();
            $optionsBlock = $this->_view->getLayout()->getBlock('easytabs.tab.options');
            if ($widget = $this->getRequest()->getParam('widget')) {
                if (is_array($widget)) {
                    if (isset($widget['widget_type'])) {
                        $optionsBlock->setWidgetType($widget['widget_type']);
                    }
                    if (isset($widget['values'])) {
                        $optionsBlock->setWidgetValues($widget['values']);
                    }
                }
            }

            // Form name is rendered into html attributes. Allow safe values only.
            $formName = $this->getRequest()->getParam('form_name');
            if (!is_string($formName) || !preg_match('/^[a-zA-Z0-9_]+$/', $formName)) {
                $formName = '';
            }
            $optionsBlock->setFormName($formName);
            $this->_view->renderLayout();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result = ['error' => true, 'message' => $e->getMessage()];
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
            );
        }
    }
}
