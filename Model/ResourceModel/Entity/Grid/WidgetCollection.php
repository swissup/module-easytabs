<?php
namespace Swissup\Easytabs\Model\ResourceModel\Entity\Grid;

/**
 * Collection for displaying grid of tabs
 */
class WidgetCollection extends Collection
{
    protected function _beforeLoad()
    {
        $this->addFieldToFilter('title', array('eq' => 'Upsells'));
        return parent::_beforeLoad();
    }
}
