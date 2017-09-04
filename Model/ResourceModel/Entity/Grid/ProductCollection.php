<?php
namespace Swissup\Easytabs\Model\ResourceModel\Entity\Grid;

/**
 * Collection for displaying grid of tabs
 */
class ProductCollection extends Collection
{
    protected function _beforeLoad()
    {
        $this->addProductTabFilter();
        // \Zend_Debug::dump($this->getSelect()->__toString());
        // \Zend_Debug::dump(__METHOD__);die;
        return parent::_beforeLoad();
    }
}
