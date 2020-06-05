<?php
namespace Swissup\Easytabs\Block;

use Magento\Store\Model\ScopeInterface;

class ProductTabs extends Tabs
{
    /**
     * {@inheritdocs}
     */
    protected function _prepareLayout()
    {
        $defaultTabs = $this->getLayout()->getBlock('product.info.details');
        if ($defaultTabs) {
            $defaultTabs->setTemplate('');
        }

        return parent::_prepareLayout();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $collection->addProductTabFilter();
        return $collection;
    }

    /**
     * Get product tabs layout name
     *
     * @return string
     */
    public function getTabsLayout()
    {
        return (string)$this->_scopeConfig->getValue(
            'swissup_easytabs/product_tabs/layout',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    protected function getJsWidgetOptions()
    {
        $options = parent::getJsWidgetOptions();
        if ($this->isAccordion()) {
            $isMultiple = $this->_scopeConfig->isSetFlag(
                'swissup_easytabs/product_tabs/multipleCollapsible',
                ScopeInterface::SCOPE_STORE
            );
            $activeTabs = $this->_scopeConfig->getValue(
                'swissup_easytabs/product_tabs/activeTabs',
                ScopeInterface::SCOPE_STORE
            );
            $options[$this->getTabsLayout()]['multipleCollapsible'] = $isMultiple;
            $options[$this->getTabsLayout()]['active'] = array_map('intval', explode(',', $activeTabs));
        }

        return $options;
    }
}
