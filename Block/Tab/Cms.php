<?php
namespace Swissup\Easytabs\Block\Tab;

class Cms extends \Magento\Framework\View\Element\Template
{
    public function getCmsBlockId()
    {
        $id = $this->getWidgetIdentifier();
        $id = is_array($id) ? reset($id) : $id;
        return $id;
    }
}
