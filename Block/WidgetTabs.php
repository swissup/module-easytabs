<?php
namespace Swissup\Easytabs\Block;

class WidgetTabs extends Tabs implements \Magento\Widget\Block\BlockInterface
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $collection->addWidgetTabFilter();
        return $collection;
    }

    public function getInitOptions()
    {
        return '{"collapsible": false, "openedState": "active"}';
    }
}
