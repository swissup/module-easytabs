<?php

namespace Swissup\Easytabs\Block\Tab\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\ViewModel\Product\Listing\PreparePostData;
use Magento\Framework\App\ObjectManager;

class Crosssell extends \Magento\Checkout\Block\Cart\Crosssell
{
    public function getItems()
    {
        $items = $this->getData('items');
        if ($items !== null) {
            return $items;
        }

        $items = [];
        $currentProduct = $this->getProduct();
        if ($currentProduct) {
            $collection = $this->_getCollection()->addProductFilter(
                $currentProduct->getData($this->getProductLinkField())
            );

            $collection->setPositionOrder()->load();

            foreach ($collection as $item) {
                $items[] = $item;
            }
        }

        $this->setData('items', $items);

        return $items;
    }

    protected function _prepareLayout()
    {
        if (class_exists(PreparePostData::class)) {
            $this->setViewModel(
                ObjectManager::getInstance()->create(PreparePostData::class)
            );
        }
    }

    private function getProductLinkField(): string
    {
        return ObjectManager::getInstance()->create(Collection::class)
            ->getProductEntityMetadata()
            ->getLinkField();
    }
}
