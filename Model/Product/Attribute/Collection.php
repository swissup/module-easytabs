<?php
namespace Swissup\Easytabs\Model\Product\Attribute;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
{
    protected function _toOptionArray($valueField = 'attribute_code', $labelField = 'frontend_label', $additional = array())
    {
        $this->addVisibleFilter();
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
