<?php

namespace GalacticLabs\CustomerGroupPaymentFilters\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Create new table to store disallowed payment options against
     * a customer group.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('customer_group_disallowed_payment_options'))
            ->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Customer Group ID'
            )
            ->addColumn(
                'disallowed_payment_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                '2K',
                ['nullable' => false],
                'Disallowed Payment Options'
            )->setComment("Customer Group Disallowed Payment Options");

        $setup->getConnection()->createTable($table);
    }
}