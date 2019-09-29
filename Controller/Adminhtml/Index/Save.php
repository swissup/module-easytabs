<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_product_save';

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $tmp = $this->getRequest()->getPost('parameters');
        if(isset($tmp['widget_identifier'])){
            $tmp['widget_identifier'] = implode(';',$tmp['widget_identifier']);
            $this->getRequest()->setPostValue('parameters', $tmp);
        }
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Swissup\Easytabs\Model\Entity $model */
            $model = $this->_objectManager->create('Swissup\Easytabs\Model\Entity');

            $id = $this->getRequest()->getParam('tab_id');
            if ($id) {
                $model->load($id);
            }

            if (empty($params['data']) && !empty($data['block_type'])) {
                $data['block'] = $data['block_type'];
            }
            unset($data['block_type']);

            if (isset($data['rule'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }

            $model->loadPost($data);

            if (isset($data['parameters'])) {
                $model->addData($data['parameters']);
            }

            $this->_eventManager->dispatch(
                'tab_prepare_save',
                ['tab' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->messageManager->addSuccess(__('Tab has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['tab_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the tab.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['tab_id' => $this->getRequest()->getParam('tab_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
