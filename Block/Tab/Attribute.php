<?php
namespace Swissup\Easytabs\Block\Tab;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as ProductAttribute;

class Attribute extends Template implements IdentityInterface
{
    /**
     * @var Magento\Catalog\Model\Product
     */
    protected $_product = null;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $_helper;

    /**
     * @param Template\Context               $context
     * @param \Magento\Framework\Registry    $registry
     * @param \Magento\Catalog\Helper\Output $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Output $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @deprecated 1.8.0 Use getAttributeCodes because it can be array
     */
    public function getAttributeCode()
    {
        return '';
    }

    /**
     * @return array
     */
    public function getAttributeCodes()
    {
        return $this->getWidgetIdentifier();
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }

        return $this->_product;
    }

    /**
     * @param  string $attributeCode
     * @return string
     */
    public function renderAttribute($attributeCode)
    {
        $product = $this->getProduct();
        if (!$product) {
            return '';
        }

        $attribute = $product->getResource()->getAttribute($attributeCode);
        if ($attribute->getFrontend()->getInputType() === 'media_image') {
            return $this->renderMediaImage($attribute, $product);
        }

        return $this->_helper->productAttribute(
            $product,
            $attribute->getFrontend()->getValue($product),
            $attributeCode
        );
    }

    /**
     * @param  string $attributeCode
     * @return string
     */
    public function renderAttributeLabel($attributeCode)
    {
        $product = $this->getProduct();
        if (!$product) {
            return '';
        }

        $attribute = $product->getResource()->getAttribute($attributeCode);

        return $attribute->getFrontend()->getLocalizedLabel();
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Swissup\Easytabs\Model\Entity::CACHE_TAG . '_' . implode('_', $this->getAttributeCodes())];
    }

    /**
     * @param  ProductAttribute $attribute
     * @param  ProductInterface $product
     * @return string
     */
    protected function renderMediaImage(
        ProductAttribute $attribute,
        ProductInterface $product
    ) {
        $file = $attribute->getFrontend()->getValue($product);
        $image = $this->getMediaImage($product, $file);

        return "<img src=\"{$image->getUrl()}\" alt=\"{$image->getLabel()}\" />";
    }

    /**
     * Get image object for product and file name
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
