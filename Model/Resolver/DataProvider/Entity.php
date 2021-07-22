<?php
declare(strict_types=1);

namespace Swissup\Easytabs\Model\Resolver\DataProvider;

use Swissup\Easytabs\Api\Data\EntityInterface;
use Swissup\Easytabs\Api\GetEntityByAliasInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Model\Template\FilterEmulate;

/**
 * tab entity data provider
 */
class Entity
{
    /**
     * @var GetEntityByAliasInterface
     */
    private $entityByAlias;

    /**
     * @var FilterEmulate
     */
    private $widgetFilter;

    /**
     *
     * @param GetEntityByAliasInterface $entityByAlias
     * @param FilterEmulate $widgetFilter
     */
    public function __construct(
        GetEntityByAliasInterface $entityByAlias,
        FilterEmulate $widgetFilter
    ) {
        $this->entityByAlias = $entityByAlias;
        $this->widgetFilter = $widgetFilter;
    }

    /**
     * Returns enity data by entity identifier
     *
     * @param string $identifier
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDataByAlias(string $identifier, int $storeId): array
    {
        $entity = $this->entityByAlias->execute($identifier, $storeId);

        return $this->convertData($entity);
    }

    /**
     * Convert data
     *
     * @param EntityInterface $entity
     * @return array
     * @throws NoSuchEntityException
     */
    private function convertData(EntityInterface $entity)
    {
        if (false === $entity->getStatus()) {
            throw new NoSuchEntityException();
        }

        $renderedContent = $this->widgetFilter
//            ->setStoreId($storeId)
            ->filter($entity->getWidgetContent());

        $data = [
            EntityInterface::TAB_ID => (int) $entity->getTabId(),
            EntityInterface::ALIAS => $entity->getAlias(),
            EntityInterface::WIDGET_CONTENT => $renderedContent,
        ];
        return $data;
    }
}
