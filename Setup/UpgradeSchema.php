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

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('swissup_easytabs_entity'),
                    'conditions_serialized',
                    array(
                        'type' => Table::TYPE_TEXT,
                        'length' => null,
                        'comment' => 'Conditions Serialized'
                    )
                );
        }

        if (version_compare($context->getVersion(), '1.8.0', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('swissup_easytabs_entity'),
                    'is_ajax',
                    array(
                        'type' => Table::TYPE_BOOLEAN,
                        'length' => null,
                        'comment' => 'Is Ajax'
                    )
                );
        }

        if (version_compare($context->getVersion(), '1.8.9', '<')) {
            $installer->getConnection()->modifyColumn(
                    $installer->getTable('swissup_easytabs_entity'),
                    'widget_identifier',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Content id/code'
                    ]
                );
        }

        $setup->endSetup();

    }
}
