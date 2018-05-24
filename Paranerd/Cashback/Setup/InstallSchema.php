<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Paranerd\Test\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {


        $installer = $setup;

        $installer->startSetup();
        //var_dump($setup);die;
        if (!$installer->tableExists('newmagento2')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('newmagento2'));
            $table->addColumn(
                    'test_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Author ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable'  => false,],
                    'Author Name'
                )

                ->addColumn(
                    'dob',
                    Table::TYPE_DATE,
                    null,
                    [],
                    'Author Birth date'
                )
                ->addColumn(
                    'awards',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Author Awards'
                )
                ->addColumn(
                    'is_active',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable'  => false,
                        'default'   => '1',
                    ],
                    'Is Author Active'
                )

                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Creation Time'
                )
                ->setComment('Test_Magento_2.0');
            $installer->getConnection()->createTable($table);


        }

    }
 }

