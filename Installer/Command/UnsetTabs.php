<?php

namespace Swissup\Easytabs\Installer\Command;

class UnsetTabs
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory
     */
    private $tabsCollectionFactory;

    /**
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->tabsCollectionFactory = $tabsCollectionFactory;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Unset specified tabs.
     *
     * @param \Swissup\Marketplace\Installer\Request $request
     */
    public function execute($request)
    {
        $this->logger->info('Easytabs: Cleanup tabs');

        $storeIdsToRemove = $request->getStoreIds();
        $storeIdsToRemove[] = 0;
        $storesToKeep = array_keys($this->storeManager->getStores());
        $storesToKeep = array_diff($storesToKeep, $storeIdsToRemove);
        $isSingleStoreMode = $this->storeManager->isSingleStoreMode();

        foreach ($request->getParams() as $unsetRules) {
            $collection = $this->tabsCollectionFactory->create();

            foreach ($unsetRules as $key => $value) {
                $collection->addFieldToFilter($key, $value);
            }
            $collection->walk('afterLoad');

            foreach ($collection as $tab) {
                if ($isSingleStoreMode) {
                    $tab->setStatus(0);
                } else {
                    $stores = $tab->getStores();
                    if (!is_array($stores)) {
                        $stores = (array) $stores;
                    }

                    $stores = array_diff($stores, [0]);

                    if (!$stores) { // tab was assigned to all stores
                        $tab->setStores($storesToKeep);
                    } else {
                        if (!array_diff($stores, $storesToKeep)) {
                            // tab is not assigned to storesToRemove
                            continue;
                        }

                        $keep = array_intersect($stores, $storesToKeep);

                        if ($keep) {
                            $tab->setStores($keep);
                        } else {
                            $tab->setStatus(0);
                        }
                    }
                }

                try {
                    $tab->save();
                } catch (\Exception $e) {
                    $this->logger->warning($e->getMessage());
                }
            }
        }
    }
}
