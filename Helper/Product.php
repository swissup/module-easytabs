<?php

namespace Swissup\Easytabs\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Relation;
use Magento\Framework\App\ObjectManager;

class Product extends \Magento\Catalog\Helper\Product
{
    /**
     * {@inheritdoc}
     */
    public function canShow($product, $where = 'catalog')
    {
        $canShow = parent::canShow($product, $where);

        // isVisibleInCatalog() checks status (enabled), not visibility.
        // So only enabled products hidden by visibility setting (e.g. "Not Visible Individually")
        // fall back to parent_id, and only when it is their real parent.
        if (!$canShow && is_object($product) && $product->isVisibleInCatalog()) {
            $parentId = (int)$this->_request->getParam('parent_id');
            $parentIds = $parentId
                ? ObjectManager::getInstance()->get(Relation::class)
                    ->getRelationsByChildren([$product->getId()])
                : [];

            if (in_array($parentId, $parentIds[$product->getId()] ?? [])) {
                $canShow = parent::canShow($parentId, $where);
            }
        }

        return $canShow;
    }
}
