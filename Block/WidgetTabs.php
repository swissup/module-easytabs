<?php
namespace Swissup\Easytabs\Block;

class WidgetTabs extends Tabs implements \Magento\Widget\Block\BlockInterface
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $filterTabs = str_replace(' ', '', $this->getFilterTabs());
        $filterTabs = explode(',', $filterTabs);
        $collection->addFieldToFilter('alias', array('in' => $filterTabs));
        $collection->addWidgetTabFilter();
        return $collection;
    }

    public function getInitOptions()
    {
        return '{"collapsible": false, "openedState": "active"}';
    }
}
