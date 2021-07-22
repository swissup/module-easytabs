<?php
declare(strict_types=1);

namespace Swissup\Easytabs\Model\Resolver\Entity;

use Swissup\Easytabs\Api\Data\EntityInterface;
use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

/**
 * Identity for resolved CMS page
 */
class Identity implements IdentityInterface
{
    /** @var string */
    private $cacheTag = \Swissup\Easytabs\Model\Entity::CACHE_TAG;

    /**
     * Get page ID from resolved data
     *
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        return empty($resolvedData[EntityInterface::TAB_ID]) ?
            [] : [$this->cacheTag, sprintf('%s_%s', $this->cacheTag, $resolvedData[EntityInterface::TAB_ID])];
    }
}
