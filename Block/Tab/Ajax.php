<?php

namespace Swissup\Easytabs\Block\Tab;

use Swissup\Easytabs\Block\Tabs;

class Ajax extends Tabs
{
    /**
     * {@inheritdoc}
     */
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $collection->addFieldToFilter('alias', $this->getTab());
        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $tabs = $this->getTabs();
        $tab = reset($tabs);

        return isset($tab['alias'])
            ? $this->getChildHtml($tab['alias'])
            : '';
    }

    /**
     * Get tab alias from request.
     *
     * @return string
     */
    public function getTab()
    {
        return $this->getRequest()->getParam('tab');
    }

    /**
     * Get product ID from request.
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam('id', false);
    }
}
