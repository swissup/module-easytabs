<?php

namespace Swissup\Easytabs\Installer\Command;

class Tabs
{
    /**
     * Blocks that render a product list. They cost more to render than the rest
     * of the tabs together and sit below the fold, so new tabs built from them
     * are lazy loaded. A definition that sets is_ajax itself still wins.
     */
    private const AJAX_BY_DEFAULT_BLOCKS = [
        \Magento\Catalog\Block\Product\ProductList\Related::class,
        \Magento\Catalog\Block\Product\ProductList\Upsell::class,
        \Swissup\Easytabs\Block\Tab\Product\Crosssell::class,
    ];

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
                // Collection doesn't unserialize fields. Do it to prevent double encoding on save.
                $tab->getResource()->unserializeFields($tab);
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
                'widget_tab' => 0,
                'is_ajax' => $this->getDefaultIsAjax($data),
                'can_listen_product' => 0,
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
     * @param array $data
     * @return int
     */
    private function getDefaultIsAjax(array $data)
    {
        $block = ltrim((string) ($data['block'] ?? ''), '\\');

        return (int) in_array($block, self::AJAX_BY_DEFAULT_BLOCKS, true);
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
