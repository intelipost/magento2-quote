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

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();

        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->getOrderExtensionDependency();
        }

        $intelipostQuote = $order->getData('intelipost_quote');

        $extensionAttributes->setIntelipostQuote($intelipostQuote);

        $order->setExtensionAttributes($extensionAttributes);
    }

    private function getOrderExtensionDependency()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Sales\Api\Data\OrderExtension'
        );
    }

}

