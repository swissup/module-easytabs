<?php
namespace Swissup\Easytabs\Api;

/**
 * Command to load the entity data by specified alias
 */
interface GetEntityByAliasInterface
{
    /**
     * Load entity data by given tab alias.
     *
     * @param string $alias
     * @param int $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Swissup\Easytabs\Api\Data\EntityInterface
     */
    public function execute(string $alias, int $storeId) : \Swissup\Easytabs\Api\Data\EntityInterface;
}
