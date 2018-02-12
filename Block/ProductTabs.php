<?php
namespace Swissup\Easytabs\Block;

use Magento\Store\Model\ScopeInterface;

class ProductTabs extends Tabs
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $collection->addProductTabFilter();
        return $collection;
    }

    public function getInitOptions()
    {
        return $this->getVar('options');
    }

    /**
     * Get product tabs layout name
     *
     * @return string
     */
    public function getTabsLayout()
    {
        return $this->_scopeConfig->getValue(
            'swissup_easytabs/product_tabs/layout',
            ScopeInterface::SCOPE_STORE
        );
    }
}
