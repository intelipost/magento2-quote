<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderLoadAfter implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtension
     */
    protected $_orderExtension;

    public function __construct(
        \Magento\Sales\Api\Data\OrderExtension $orderExtension
    ) {
        $this->_orderExtension = $orderExtension;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();

        $extensionAttributes = $order->getExtensionAttributes();

        $intelipostQuote = $order->getData('intelipost_quote');

        $this->_orderExtension->setIntelipostQuote($intelipostQuote);

        $order->setExtensionAttributes($extensionAttributes);
    }
}
