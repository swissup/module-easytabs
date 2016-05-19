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
     * Constructor
     *
     * @param \Magento\Framework\View\ElementTemplate\Context $context
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    )
    {
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $storeId = $this->_storeManager->getStore()->getId();
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
