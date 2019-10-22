<?php

namespace Swissup\Easytabs\Model\Product\Attribute\Renderer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class MediaImage extends General
{
    /**
     * {@inheritdocs}
     */
    public function render(Attribute $attribute, ProductInterface $product)
    {
        $file = $attribute->getFrontend()->getValue($product);
        $image = $this->getMediaImage($product, $file);

        return "<img src=\"{$image->getUrl()}\" alt=\"{$image->getLabel()}\" />";
    }

    /**
     * Get image object for product and file name.
     *
     * @param  ProductInterface              $product
     * @param  string                        $file
     * @return \Magento\Framework\DataObject
     */
    private function getMediaImage(ProductInterface $product, $file)
    {
        $image = [];
        if (is_array($product->getMediaGallery('images'))) {
            foreach ($product->getMediaGallery('images') as $item) {
                if (isset($item['file']) && $item['file'] === $file) {
                    $mediaConfig = $product->getMediaConfig();
                    $image = $item;
                    $image['url'] = $mediaConfig->getMediaUrl($item['file']);
                    break;
                }
            }
        }

        return new \Magento\Framework\DataObject($image);
    }
}
