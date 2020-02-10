<?php

namespace Swissup\Easytabs\Installer\Command;

class Tabs
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory
     */
    private $tabsCollectionFactory;

    /**
     * @param \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
     */
    public function __construct(
        \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
    ) {
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
     * Create new tabs.
     * If duplicate is found - do nothing.
     *
     * @param \Swissup\Marketplace\Installer\Request $request
     */
    public function execute($request)
    {
        $this->logger->info('Easytabs: Create tabs');

        foreach ($request->getParams() as $data) {
            $tab = $this->tabsCollectionFactory
                ->create()
                ->addFieldToFilter('alias', $data['alias'])
                ->addFieldToFilter('block', $data['block'])
                ->getFirstItem();

            if ($tab->getId()) {
                $storeIds = array_unique(
                    array_merge($tab->getStores(), $request->getStoreIds())
                );

                if (!array_diff($storeIds, $tab->getStores())) {
                    // tab is already assigned to requested store
                    continue;
                }
            } else {
                $tab->setData($data);
                $storeIds = $request->getStoreIds();
            }

            try {
                $tab->setStores($storeIds)->save();
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage());
            }
        }
    }
}
