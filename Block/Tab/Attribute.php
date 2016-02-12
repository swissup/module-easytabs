<?php
namespace Swissup\Easytabs\Block\Tab;

use Magento\Framework\DataObject\IdentityInterface;

class Attribute extends \Magento\Framework\View\Element\Template
    implements IdentityInterface
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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function getAttributeCode()
    {
        return $this->getWidgetIdentifier();
    }
    /**
     * @return Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Swissup\Easytabs\Model\Entity::CACHE_TAG . '_' . $this->getAttributeCode()];
    }
}
