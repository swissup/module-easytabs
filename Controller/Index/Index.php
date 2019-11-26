<?php

namespace Swissup\Easytabs\Controller\Index;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Catalog\Controller\Product
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->getBlock('easytabs.tab.ajax');
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents($block->toHtml());

        return $resultRaw;
    }
}
