<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Intelipost Quote
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('intelipost_quote'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'session_id',
                Table::TYPE_TEXT,
                255,
                [],
                'Session ID'
            )
            ->addColumn(
                'quote_id',
                Table::TYPE_TEXT,
                255,
                [],
                'Quote ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_TEXT,
                255,
                [],
                'Order ID'
            )
            ->addColumn(
                'carrier',
                Table::TYPE_TEXT,
                255,
                [],
                'Carrier'
            )
            ->addColumn(
                'shipping_method',
                Table::TYPE_TEXT,
                255,
                [],
                'Shipping Method'
            )
            ->addColumn(
                'products',
                Table::TYPE_TEXT,
                null,
                [],
                'Products'
            )
            ->addColumn(
                'logistic_provider_name',
                Table::TYPE_TEXT,
                255,
                [],
                'Logistic Provider Name'
            )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                255,
                [],
                'Description'
            )
            ->addColumn(
                'delivery_method_id',
                Table::TYPE_TEXT,
                255,
                [],
                'Delivery Method ID'
            )
            ->addColumn(
                'delivery_estimate_business_days',
                Table::TYPE_TEXT,
                255,
                [],
                'Delivery Estimate Business Days'
            )
            ->addColumn(
                'available_scheduling_dates',
                Table::TYPE_TEXT,
                null,
                [],
                'Available Scheduling Dates'
            )
            ->addColumn(
                'selected_scheduling_dates',
                Table::TYPE_TEXT,
                null,
                [],
                'Selected Scheduling Dates'
            )
            ->addColumn(
                'selected_scheduling_period',
                Table::TYPE_TEXT,
                null,
                [],
                'Selected Scheduling Period'
            )
            ->addColumn(
                'provider_shipping_cost',
                Table::TYPE_TEXT,
                255,
                [],
                'Provider Shipping Cost'
            )
            ->addColumn(
                'final_shipping_cost',
                Table::TYPE_TEXT,
                255,
                [],
                'Final Shipping Cost'
            )
            ->addColumn(
                'api_request',
                Table::TYPE_TEXT,
                null,
                [],
                'API Request'
            )
            ->addColumn(
                'api_response',
                Table::TYPE_TEXT,
                null,
                [],
                'API Response'
            )
            ->setComment(
                'Intelipost Quote'
            );
        $installer->getConnection()
            ->createTable($table);

        /*
         * Sales Order
         */
        $result = $installer->getConnection()
            ->addColumn(
                $installer->getTable('sales_order'),
                'intelipost_quote',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Intelipost Quote'
                ]
            );

        $installer->endSetup();
    }
}
