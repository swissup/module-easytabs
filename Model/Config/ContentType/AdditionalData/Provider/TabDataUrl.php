<?php

declare(strict_types=1);

namespace Swissup\Easytabs\Model\Config\ContentType\AdditionalData\Provider;

use Swissup\Easytabs\Model\Config\ContentType\AdditionalData\ProviderInterface;

/**
 * Provides URL for retrieving block metadata
 */
class TabDataUrl implements ProviderInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * BlockDataUrl constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(\Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getData(string $itemName) : array
    {
        return [$itemName => $this->urlBuilder->getUrl('easytabs/contenttype_tab/metadata')];
    }
}
