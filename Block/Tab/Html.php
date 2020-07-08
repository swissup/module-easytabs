<?php
namespace Swissup\Easytabs\Block\Tab;

class Html extends \Magento\Framework\View\Element\Template
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
}
