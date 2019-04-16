<?php

namespace Swissup\Easytabs\Model\Rule\Condition;

class Product extends \Magento\CatalogRule\Model\Rule\Condition\Product
{
    /**
     * {@inheritdoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $tab)
    {
        $product = $tab->getProduct();
        if (!$product) {
            return true;
        }

        return parent::validate($tab->getProduct());
    }
}
