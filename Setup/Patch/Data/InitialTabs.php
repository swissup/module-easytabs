<?php

namespace Swissup\Easytabs\Setup\Patch\Data;


use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Store\Model\Store;

class InitialTabs implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * PatchInitial constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $entityTable = $this->moduleDataSetup->getTable('swissup_easytabs_entity');
        $storeTable = $this->moduleDataSetup->getTable('swissup_easytabs_store');
        foreach ($this->getEntities() as $entity) {
            $connection->insert($entityTable, $entity);
            $entityId = $connection->lastInsertId($entityTable);
            $connection->insert(
                $storeTable,
                [ 'tab_id' => $entityId, 'store_id' => Store::DEFAULT_STORE_ID ]
            );
        }
    }

    /**
     * Get data to insert into DB table
     *
     * @return array
     */
    private function getEntities() {
        return [
            [
                'title' => 'Details',
                'alias' => 'product.info.description',
                'block' => 'Magento\Catalog\Block\Product\View\Description',
                'sort_order' => 0,
                'status' => 1,
                'widget_template' => 'Magento_Catalog::product/view/description.phtml'
            ],
            [
                'title' => 'More Information',
                'alias' => 'additional',
                'block' => 'Magento\Catalog\Block\Product\View\Attributes',
                'sort_order' => 10,
                'status' => 1,
                'widget_template' => 'Magento_Catalog::product/view/attributes.phtml',
                'widget_unset' => 'product.attributes.wrapper' // unset block when page layout is "Product -- Full Width"
            ],
            [
                'title' => '{{eval code="getTabTitle()"}}',
                'alias' => 'reviews',
                'block' => 'Swissup\Easytabs\Block\Tab\Product\Review',
                'sort_order' => 20,
                'status' => 1,
                'widget_template' => 'Magento_Review::review.phtml',
                'widget_unset' => 'product.reviews.wrapper' // unset block when page layout is "Product -- Full Width"
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
