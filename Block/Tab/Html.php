<?php
namespace Swissup\Easytabs\Block\Tab;

use Magento\Framework\DataObject\IdentityInterface;

class Html extends \Magento\Framework\View\Element\Template
     implements IdentityInterface
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\ElementTemplate\Context $context
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->filterProvider = $filterProvider;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $html = $this->filterProvider->getBlockFilter()
            ->setStoreId($storeId)
            ->filter($this->getWidgetContent());
        return $html;
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Swissup\Easytabs\Model\Entity::CACHE_TAG . '_' . $this->getNameInLayout()];
    }
}
