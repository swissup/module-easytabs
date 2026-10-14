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
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $tab = $this->_request->getParam('tab');
        if (!is_string($tab) || $tab === '') {
            return $this->noRoute();
        }

        $pathAlias = $this->_request->getParam('path_alias', '');
        $this->_request->setAlias(
            UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $this->decoder->decode(is_string($pathAlias) ? $pathAlias : '')
        );

        // Product requested but not available (disabled, not visible, wrong website).
        // Do not render tabs without product: product conditions would be skipped.
        if ((int)$this->_request->getParam('id') && !$this->_initProduct()) {
            return $this->noRoute();
        }

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Forward
     */
    private function noRoute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
    }

    /**
     * Initialize requested product object
     *
     * @return ModelProduct|false
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
