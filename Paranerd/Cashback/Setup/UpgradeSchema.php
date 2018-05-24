<?php

namespace Paranerd\Cashback\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface    $context)
	{
    $installer = $setup;

    $installer->startSetup();

	$cashbackTable = $installer->getTable('pending_cashback');

	if ($installer->getConnection()->isTableExists($cashbackTable) != true) {
		$table = $installer->getConnection()
			->newTable($cashbackTable)
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'identity' => true,
					'unsigned' => true,
					'nullable' => false,
					'primary' => true
				],
				'ID'
			)
			->addColumn(
				'customer_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'nullable' => false,
				],
				'Customer ID'
			)
			->addColumn(
				'order_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'nullable' => false,
				],
				'Order ID'
			)
			->addColumn(
				'amount',
				\Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
				null,
				[
					'nullable' => false,
				],
				'Amount'
			)
			->addColumn(
				'created_at',
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				[
					'nullable' => false,
					'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
				],
				'Created At'
			)
			->addColumn(
				'completed_at',
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				[
					'nullable' => true,
				],
				'Completed At'
			)
			->setComment('Pending Cashback Table')
			->setOption('type', 'InnoDB')
			->setOption('charset', 'utf8');
		$installer->getConnection()->createTable($table);
	}

    $eavTable = $installer->getTable('customer_entity');

    $columns = [
        'cashback' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
			'length' => 255,
            'nullable' => false,
            'comment' => 'Cashback',
			'default' => 0
        ]
    ];

    $connection = $installer->getConnection();
    foreach ($columns as $name => $definition) {
        $connection->addColumn($eavTable, $name, $definition);
    }

    $installer->endSetup();
}
}