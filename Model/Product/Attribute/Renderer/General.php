<?php

namespace Swissup\Easytabs\Model\Product\Attribute\Renderer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class General
{
    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $helper;

    /**
     * @param \Magento\Catalog\Helper\Output $helper
     */
    public function __construct(
        \Magento\Catalog\Helper\Output $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Render attribute content for product attribute tab.
     *
     * @param  Attribute        $attribute
     * @param  ProductInterface $product
     */
    public function render(Attribute $attribute, ProductInterface $product)
    {
        return $this->helper->productAttribute(
            $product,
            $attribute->getFrontend()->getValue($product),
            $attribute->getAttributeCode()
        );
    }
}
