<?php
namespace Swissup\Easytabs\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'swissup_easytabs_entity'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_easytabs_entity'))
            ->addColumn(
                'tab_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ],
                'Tab ID'
            )
            ->addColumn('title', Table::TYPE_TEXT, 100, ['nullable' => false], 'Tab title')
            ->addColumn('alias', Table::TYPE_TEXT, 100, ['nullable' => false], 'Tab alias')
            ->addColumn('block', Table::TYPE_TEXT, 100, ['nullable' => false], 'Tab block name')
            ->addColumn('sort_order', Table::TYPE_SMALLINT, null, ['nullable' => true, 'default' => '0'], 'Tab sort order')
            ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Tab status')
            ->addColumn('widget_template', Table::TYPE_TEXT, 200, ['nullable' => true], 'Tab content template')
            ->addColumn('widget_unset', Table::TYPE_TEXT, 100, ['nullable' => true], 'Unset child name')
            ->addColumn('widget_identifier', Table::TYPE_TEXT, 100, ['nullable' => true], 'Content id/code')
            ->addColumn('widget_block', Table::TYPE_TEXT, 100, ['nullable' => true], 'Widget block name')
            ->addColumn('widget_content', Table::TYPE_TEXT, null, ['nullable' => true], 'Widget content')
            ->addColumn('block_arguments', Table::TYPE_TEXT, null, ['nullable' => true], 'Widget block arguments')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Tab created date'
            )
            ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Tab update time'
            )
            ->addIndex(
                $setup->getIdxName(
                    $installer->getTable('swissup_easytabs_entity'),
                    ['title', 'block', 'widget_template'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['title', 'block', 'widget_template'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )
            ->setComment('Swissup Easy Tabs Entity Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_easytabs_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_easytabs_store')
        )->addColumn(
            'tab_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Tab ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('swissup_easytabs_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('swissup_easytabs_store', 'tab_id', 'swissup_easytabs_entity', 'tab_id'),
            'tab_id',
            $installer->getTable('swissup_easytabs_entity'),
            'tab_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_easytabs_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Swissup Easy Tabs To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
