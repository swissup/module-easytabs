<?php

declare(strict_types=1);

// @codingStandardsIgnoreStart
namespace Swissup\Easytabs\Model\Config\ContentType\AdditionalData;

use Magento\PageBuilder\Model\Config\ContentType\AdditionalData\ProviderInterface as OriginalProviderInterface;

/**
 * Provides runtime-specific data for additional data content types configuration
 *
 * @api
 */
if (interface_exists(OriginalProviderInterface::class)) {
    interface ProviderInterface extends OriginalProviderInterface
    {
    }
} else {
    interface ProviderInterface
    {
        /**
         * Get data from the provider
         * @param string $itemName - the name of the item to use as key in returned array
         * @return array
         */
        public function getData(string $itemName): array;
    }
}
// @codingStandardsIgnoreEnd
