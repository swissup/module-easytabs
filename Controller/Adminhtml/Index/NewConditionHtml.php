<?php

namespace Swissup\Easytabs\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Rule\Model\Condition\AbstractCondition;

class NewConditionHtml extends \Magento\Backend\App\Action
{
    /**
     * Admin resource
     */
    const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_save';

    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $formName = $this->getSafeParam('form_namespace');
        $typeArr = explode('|', str_replace('-', '/', (string)$this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        // Validate class before instantiating it: never create arbitrary classes from request.
        if (!$type || !is_a($type, AbstractCondition::class, true)) {
            $this->getResponse()->setBody('');
            return;
        }

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->_objectManager->create(\Magento\CatalogRule\Model\Rule::class))
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        $model->setJsFormObject($this->getSafeParam('form'));
        $model->setFormName($formName);

        $this->getResponse()->setBody($model->asHtmlRecursive());
    }

    /**
     * Get request param allowed to be used as html id or js identifier.
     *
     * @param  string $key
     * @return string
     */
    private function getSafeParam($key)
    {
        $value = $this->getRequest()->getParam($key);

        return is_string($value) && preg_match('/^[a-zA-Z0-9_]+$/', $value) ? $value : '';
    }
}
