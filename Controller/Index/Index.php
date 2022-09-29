<?php

namespace Swissup\Easytabs\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Url\DecoderInterface;
use Magento\Catalog\Controller\Product\View\ViewInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Swissup\Easytabs\Helper\Product as HelperProduct;

class Index extends \Magento\Framework\App\Action\Action implements ViewInterface
{
    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var HelperProduct
     */
    private $helper;

    /**
     * @param DecoderInterface $decoder
     * @param HelperProduct    $helper
     * @param Context          $context
     */
    public function __construct(
        DecoderInterface $decoder,
        HelperProduct $helper,
        Context $context
    ) {
        $this->decoder = $decoder;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->_request->setAlias(
            UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $this->decoder->decode($this->_request->getParam('path_alias', ''))
        );
        $this->_initProduct();

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }

    /**
     * Initialize requested product object
     *
     * @return ModelProduct
     */
    protected function _initProduct()
    {
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $productId = (int)$this->getRequest()->getParam('id');

        $params = new \Magento\Framework\DataObject();
        $params->setCategoryId($categoryId);

        return $this->helper->initProduct($productId, $this, $params);
    }
}
