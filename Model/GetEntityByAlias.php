<?php
namespace Swissup\Easytabs\Model;

use Swissup\Easytabs\Api\Data\EntityInterface;
use Swissup\Easytabs\Api\GetEntityByAliasInterface;
use Swissup\Easytabs\Model\Entity as TabModel;
use Swissup\Easytabs\Model\ResourceModel\Entity\Collection as TabsCollection;
use Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory as TabsCollectionFactory;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GetEntityByAliasInterface
 */
class GetEntityByAlias implements GetEntityByAliasInterface
{
    /**
     * Get tabs collection
     * @var \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory
     */
    private $tabsCollectionFactory;

    /**
     * @param TabsCollectionFactory  $tabsCollectionFactory
     */
    public function __construct(
        TabsCollectionFactory $tabsCollectionFactory
    ) {
        $this->tabsCollectionFactory = $tabsCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $alias, int $storeId) : EntityInterface
    {
        $collection = $this->tabsCollectionFactory->create();
        $collection
            ->addOrder(EntityInterface::SORT_ORDER, TabsCollection::SORT_ORDER_DESC)
            ->addStatusFilter(TabModel::STATUS_ENABLED)
            ->addStoreFilter($storeId)
            ->addFieldToFilter('alias', $alias);

        $entity = $collection->getFirstItem();

        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('The tab entity with the "%1" alias doesn\'t exist.', $alias));
        }

        return $entity;
    }
}
