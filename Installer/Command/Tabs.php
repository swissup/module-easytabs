<?php

namespace Swissup\Easytabs\Installer\Command;

class Tabs
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Swissup\Easytabs\Model\EntityFactory
     */
    private $tabFactory;

    /**
     * @var \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory
     */
    private $tabsCollectionFactory;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Swissup\Easytabs\Model\EntityFactory $tabFactory
     * @param \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManager $storeManager,
        \Swissup\Easytabs\Model\EntityFactory $tabFactory,
        \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory $tabsCollectionFactory
    ) {
        $this->localeDate = $localeDate;
        $this->storeManager = $storeManager;
        $this->tabFactory = $tabFactory;
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

        $isSingleStoreMode = $this->storeManager->isSingleStoreMode();

        foreach ($request->getParams() as $data) {
            $tabs = $this->tabsCollectionFactory
                ->create()
                ->addFieldToFilter('alias', $data['alias'])
                ->addFieldToFilter('block', $data['block'])
                ->addStoreFilter($request->getStoreIds());

            foreach ($tabs as $tab) {
                $storesToLeave = array_diff($tab->getStores(), $request->getStoreIds());

                if (count($storesToLeave) && !$isSingleStoreMode) {
                    $tab->setStores($storesToLeave);
                } else {
                    $tab->setStatus(0)
                        ->setAlias($this->getBackupAlias($tab->getAlias()));
                }

                try {
                    $tab->save();
                } catch (\Exception $e) {
                    $this->logger->warning($e->getMessage());
                }
            }

            $data = array_merge([
                'status' => 1,
            ], $data);

            try {
                $this->tabFactory->create()
                    ->setData($data)
                    ->setStores($request->getStoreIds())
                    ->save();
            } catch (\Exception $e) {
                $this->logger->warning($e->getMessage());
            }
        }
    }

    /**
     * @param string $alias
     * @return string
     */
    private function getBackupAlias($alias)
    {
        return $alias
            . '_backup_'
            . rand(10, 99)
            . '_'
            . $this->localeDate->date()->format('Y-m-d-H-i-s');
    }
}
