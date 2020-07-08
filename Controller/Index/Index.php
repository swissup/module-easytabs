<?php

namespace Swissup\Easytabs\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Catalog\Controller\Product
{
    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->_initProduct();

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
