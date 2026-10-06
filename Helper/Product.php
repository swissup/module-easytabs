<?php

namespace Swissup\Easytabs\Helper;

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
            if ($parentId && parent::canShow($parentId, $where)) {
                $parent = $this->productRepository->getById($parentId);
                // [group => [childId => childId]] for configurable, grouped and bundle.
                $childIds = $parent->getTypeInstance()->getChildrenIds($parentId, false);
                $canShow = in_array($product->getId(), array_merge([], ...array_values($childIds)));
            }
        }

        return $canShow;
    }
}
