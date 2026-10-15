<?php

namespace Swissup\Easytabs\Block\Tab;

use Magento\Framework\Registry;
use Magento\Framework\Url\EncoderInterface;
use Swissup\Easytabs\Block\Tabs;
use Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory as TabsCollectionFactory;

class Ajax extends Tabs
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param TabsCollectionFactory                            $tabsCollectionFactory
     * @param \Swissup\Easytabs\Model\Template\Filter          $templateFilter
     * @param \Magento\Framework\Module\FullModuleList         $fullModuleList
     * @param \Magento\Framework\Module\Manager                $moduleManager
     * @param EncoderInterface                                 $encoder
     * @param Registry                                         $registry
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        TabsCollectionFactory $tabsCollectionFactory,
        \Swissup\Easytabs\Model\Template\Filter $templateFilter,
        \Magento\Framework\Module\FullModuleList $fullModuleList,
        \Magento\Framework\Module\Manager $moduleManager,
        EncoderInterface $encoder,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct(
            $context,
            $tabsCollectionFactory,
            $templateFilter,
            $fullModuleList,
            $moduleManager,
            $encoder,
            $data
        );
    }

    /**
     * {@inheritdoc}
     *
     * Add product tags: cached ajax response must be purged when product is saved.
     */
    public function getIdentities()
    {
        $result = parent::getIdentities();
        $product = $this->registry->registry('product');
        if ($product) {
            $result = array_merge($result, $product->getIdentities());
        }

        return array_values(array_unique($result));
    }

    /**
     * {@inheritdoc}
     */
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $collection->addFieldToFilter('alias', $this->getTab());

        // Without product only widget tabs are allowed (widget on non-product page).
        // Product tabs require product, otherwise product conditions are skipped.
        if (!(int)$this->getProductId()) {
            $collection->addWidgetTabFilter();
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $tabs = $this->getTabs();
        $tab = reset($tabs);

        return isset($tab['alias'])
            ? $this->getChildHtml($tab['alias'])
            : '';
    }

    /**
     * Get tab alias from request.
     *
     * @return string
     */
    public function getTab()
    {
        $tab = $this->getRequest()->getParam('tab');

        return is_string($tab) ? $tab : '';
    }

    /**
     * Get product ID from request.
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam('id', false);
    }
}
