<?php

namespace Swissup\Easytabs\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Url\DecoderInterface;

class Index extends \Magento\Catalog\Controller\Product
{
    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @param DecoderInterface $decode
     * @param Context $context
     */
    public function __construct(
        DecoderInterface $decoder,
        Context $context
    ) {
        $this->decoder = $decoder;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->_request->setAlias(
            UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $this->decoder->decode($this->_request->getParam('path_alias'))
        );
        $this->_initProduct();

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
