<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderSaveBefore implements ObserverInterface
{

protected $_intelipostQuote;
protected $_intelipostHelper;
protected $_sessionManager;

public function __construct(
    \Intelipost\Quote\Model\Quote $intelipostQuote,
    \Intelipost\Quote\Helper\Data $intelipostHelper,
    \Magento\Framework\Session\SessionManager $sessionManager
)
{
    $this->_intelipostQuote = $intelipostQuote;
    $this->_intelipostHelper = $intelipostHelper;
    $this->_sessionManager = $sessionManager;
}

public function execute(Observer $observer)
{
    return null;
/*
    $sessionId = $this->_sessionManager->getSessionId ();

    $intelipostQuoteCollection = $this->_intelipostQuote->getCollection();
    $intelipostQuoteCollection->getSelect()->where("session_id = '{$sessionId}' AND order_id IS NOT NULL");

    if (!$intelipostQuoteCollection->count()) return;
*/
    $resultQuotes = $this->_intelipostHelper->getResultQuotes();
    if(empty($resultQuotes)) return;
    else $intelipostQuoteCollection = $resultQuotes;

    $orderInstance = $observer->getOrder();
    $shippingMethod = $orderInstance->getShippingMethod();

    foreach ($intelipostQuoteCollection as $quoteItem)
    {
        $intelipostQuote = $quoteItem; // $this->_intelipostQuote->load ($quoteItem->getId ());

        if(!strcmp($shippingMethod, $intelipostQuote->getShippingMethod()) && $intelipostQuote->getAvailableSchedulingDates()
            && (!$intelipostQuote->getSelectedSchedulingDates() || !$intelipostQuote->getSelectedSchedulingPeriod()))
        {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please select a date to pick up your order')
            );
        }
    }
}

}

