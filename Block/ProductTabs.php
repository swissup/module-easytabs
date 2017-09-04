<?php
namespace Swissup\Easytabs\Block;

use Swissup\Easytabs\Api\Data\EntityInterface;
use Swissup\Easytabs\Model\Entity as TabsModel;
use Swissup\Easytabs\Model\ResourceModel\Entity\Collection as TabsCollection;

class ProductTabs extends Tabs
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $collection->addProductTabFilter();
        return $collection;
    }
}
