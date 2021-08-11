<?php

namespace Swissup\Easytabs\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;

class Index extends \Magento\Catalog\Controller\Product
{
    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->_initProduct();
        $this->_request->setAlias(
            UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $this->_request->getParam('path_alias')
        );

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
