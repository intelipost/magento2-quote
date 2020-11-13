<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('intelipost_quote'),
                    'delivery_exact_estimated_date',
                    [
                        'type' => Table::TYPE_DATE,
                        'comment' => 'Delivery Exact Estimated Date'
                    ]
                );
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('intelipost_quote'),
                    'delivery_method_name',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Delivery Method Name'
                    ]
                );
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('intelipost_quote'),
                    'delivery_method_type',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Delivery Method Type'
                    ]
                );
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('intelipost_quote'),
                'quote_volume',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Volume Quote Information'
                ]
            );

            $table = $setup->getConnection()->newTable(
                $setup->getTable('intelipost_shipment')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'order_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Order Number'
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Entity Id'
            )->addColumn(
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Quote Id'
            )->addColumn(
                'delivery_method_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Delivery Method Id'
            )->addColumn(
                'delivery_estimate_business_days',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Delivery Estimate Business Days'
            )->addColumn(
                'shipment_order_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Shipment Order Type'
            )->addColumn(
                'shipment_order_sub_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Shipment Order Sub Type'
            )->addColumn(
                'delivery_method_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Delivery Method Type'
            )->addColumn(
                'delivery_method_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Delivery Method Name'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Description'
            )->addColumn(
                'sales_channel',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Sales Channel'
            )->addColumn(
                'provider_shipping_costs',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Provider Shipping Costs'
            )->addColumn(
                'customer_shipping_costs',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Customer Shipping Costs'
            )->addColumn(
                'intelipost_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Intelipost Status'
            )->addColumn(
                'volumes',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Volumes'
            )->addColumn(
                'scheduled',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                255,
                [],
                'Scheduled'
            )->addColumn(
                'delivery_estimate_date_exact_iso',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Delivery Estimate Date Exact'
            )->addColumn(
                'scheduling_window_start',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Scheduling Window Start'
            )->addColumn(
                'scheduling_window_end',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Scheduling Window End'
            )->addColumn(
                'selected_scheduling_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Selected Scheduling Date'
            )->addColumn(
                'tracking_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Tracking Code'
            )->addColumn(
                'intelipost_message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Intelipost Message'
            )->addColumn(
                'products_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Products Ids'
            )->setComment(
                'Intelipost Shipment Order'
            );

            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.3.0') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('intelipost_quote'),
                    'origin_zip_code',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Warehouse origin zip code'
                    ]
                );
        }

        $setup->endSetup();
    }
}
