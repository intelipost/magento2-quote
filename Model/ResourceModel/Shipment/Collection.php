<?php

namespace Intelipost\Quote\Model\ResourceModel\Shipment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */

    protected $_idFieldName = 'id';
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'Intelipost\Quote\Model\Shipment',
            'Intelipost\Quote\Model\ResourceModel\Shipment'
        );
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->storeManager = $storeManager;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['so' => $this->getTable('sales_order')],
            'main_table.entity_id = so.entity_id',
            ['created_at', 'shipping_amount', 'status', 'customer_firstname', 'customer_lastname', 'customer_email', 'customer_taxvat', 'base_grand_total', 'increment_id']
        );
    }
}
