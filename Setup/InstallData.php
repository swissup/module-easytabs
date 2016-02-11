<?php
namespace Swissup\Easytabs\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Swissup\Easytabs\Model\EntityFactory as TabFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Tab factory
     *
     * @var TabFactory
     */
    private $tabFactory;

    /**
     * Init
     *
     * @param TabFactory $tabFactory
     */
    public function __construct(TabFactory $tabFactory)
    {
        $this->tabFactory = $tabFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $tabs = [
            [
                'title' => 'Details',
                'alias' => 'product.info.description',
                'block' => 'Magento\Catalog\Block\Product\View\Description',
                'sort_order' => 0,
                'status' => 1,
                'widget_template' => 'Magento_Catalog::product/view/description.phtml',
                'stores' => [0]
            ],
            [
                'title' => 'More Information',
                'alias' => 'additional',
                'block' => 'Magento\Catalog\Block\Product\View\Attributes',
                'sort_order' => 10,
                'status' => 1,
                'widget_template' => 'Magento_Catalog::product/view/attributes.phtml',
                'stores' => [0]
            ],
            [
                'title' => '{{eval code="getTabTitle()"}}',
                'alias' => 'reviews',
                'block' => 'Swissup\Easytabs\Block\Tab\Product\Review',
                'sort_order' => 20,
                'status' => 1,
                'widget_template' => 'Magento_Review::review.phtml',
                'stores' => [0]
            ]
        ];
        /**
         * Insert default tabs
         */
        foreach ($tabs as $data) {
            $this->tabFactory->create()->setData($data)->save();
        }

    }
}
