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

        // Without product only widget tabs are allowed (widget on non-product page).
        // Product tabs require product, otherwise product conditions are skipped.
        if (!(int)$this->getProductId()) {
            $collection->addWidgetTabFilter();
        }

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
        $tab = $this->getRequest()->getParam('tab');

        return is_string($tab) ? $tab : '';
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
