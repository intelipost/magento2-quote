<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class IntelipostQuoteSaveCommitAfterObserver implements ObserverInterface
{
    protected $_collectionFactory;

    public function __construct(\Intelipost\Quote\Model\Resource\Shipment\CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $entity_id = $order->getEntityId();
        $order_number = $order->getIncrementId();

        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('order_number', ['like' => $order_number . '%']);

        if ($collection->count()) {
            foreach ($collection as $value) {
                $value->setEntityId($entity_id);
                $value->save();
            }
        }
    }
}
