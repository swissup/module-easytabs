<?php
namespace Swissup\Easytabs\Block\Tab;

use Magento\Framework\DataObject\IdentityInterface;

class Cms extends \Magento\Framework\View\Element\Template
     implements IdentityInterface
{
    public function getCmsBlockId()
    {
        return $this->getWidgetIdentifier();
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Swissup\Easytabs\Model\Entity::CACHE_TAG . '_' . $this->getCmsBlockId()];
    }
}
