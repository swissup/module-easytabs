<?php declare(strict_types=1);

namespace Swissup\Easytabs\Model\Product\Attribute\Renderer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;

class Categories
{
    public function __construct(
        protected Escaper $escaper,
        protected StoreManagerInterface $storeManager
    ) {
    }

    public function render(Attribute $attribute, ProductInterface $product)
    {
        $currentStoreRootId = (int) $this->storeManager->getStore()->getRootCategoryId();

        $categories = $product->getCategoryCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('is_active', 1);

        if (!$categories->getSize()) {
            return '';
        }

        $categoryNames = [];
        foreach ($categories as $category) {
            $categoryId = (int) $category->getId();
            if ($categoryId <= 2 || $categoryId === $currentStoreRootId) {
                continue;
            }
            $categoryNames[] = $category->getName();
        }
        if (empty($categoryNames)) {
            return '';
        }

        return $this->escaper->escapeHtml(implode(', ', $categoryNames));
    }
}