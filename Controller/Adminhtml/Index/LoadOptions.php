<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

class LoadOptions extends \Magento\Backend\App\Action
{
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

            $formName = $this->getRequest()->getParam('form_name');
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
