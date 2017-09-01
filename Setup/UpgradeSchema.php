<?php
namespace Swissup\Easytabs\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        $installer = $setup;
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('swissup_easytabs_entity'),
                    'widget_tab',
                    array(
                        'type' => Table::TYPE_BOOLEAN,
                        'length' => null,
                        'comment' => 'Widget Tab'
                    )
                );
        }

        $setup->endSetup();

    }
}
