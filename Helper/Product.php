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

        if (!$canShow) {
            $parentId = (int)$this->_request->getParam('parent_id');

            if ($parentId) {
                $canShow = parent::canShow($parentId, $where);
            }
        }

        return $canShow;
    }
}
