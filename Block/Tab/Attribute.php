<?php
namespace Swissup\Easytabs\Block\Tab;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as ProductAttribute;

class Attribute extends Template
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
     * @param Template\Context            $context
     * @param \Magento\Framework\Registry $registry
     * @param array                       $attributeRenderer
     * @param array                       $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $attributeRenderer = [],
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $data['attributeRenderer'] = $attributeRenderer;
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
        $value = $this->getWidgetIdentifier();
        return is_array($value) ? $value : [$value];
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
        $type = $attribute->getFrontend()->getInputType();
        $renderer = $this->getData("attributeRenderer/{$type}")
            ?: $this->getData('attributeRenderer/general');

        return $renderer ? $renderer->render($attribute, $product) : '';
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
}
