<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Controller\Schedule;

class Status extends \Magento\Framework\App\Action\Action
{

/**
* @var \Magento\Framework\Stdlib\CookieManagerInterface
*/
protected $_cookieManager;
protected $_checkoutSession;

protected $_quoteFactory;
protected $_quoteHelper;

public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    \Intelipost\Quote\Model\QuoteFactory $quoteFactory,
    \Intelipost\Quote\Helper\Data $quoteHelper,
    \Magento\Checkout\Model\Session $checkouSession,
    \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
)
{
    $this->_quoteFactory = $quoteFactory;
    $this->_quoteHelper = $quoteHelper;

    $this->_resultPageFactory = $resultPageFactory;

    $this->_checkoutSession = $checkouSession;
    $this->_cookieManager = $cookieManager;

    parent::__construct ($context);
}

public function executeOld()
{
/*
    $sessionId = $this->_quoteHelper->getSessionId();

    $collection = $this->_quoteFactory->create()->getCollection();
    $collection->getSelect()->where("session_id = '{$sessionId}' AND selected_scheduling_dates IS NOT NULL");

    if (!$collection->count()) return null;
*/
    $item = null;

    foreach ($this->_quoteHelper->getResultQuotes() as $quote)
    {
        if (!empty($quote->getSelectedSchedulingDates()))
        {
            $item = $quote;

            break;
        }
    }

    if(empty($item)) return null;

    // $item = $collection->getFirstItem();

    $selDate = $item->getSelectedSchedulingDates();
    $period  = $item->getSelectedSchedulingPeriod();

    $resultPage = $this->_resultPageFactory->create();
    $this->getResponse()->setBody(
        __('Delivery Scheduled for: %1 period: %2', $selDate, __(ucfirst($period)))
    );
}

public function execute()
{
    $session = $this->_checkoutSession;
    $quote = $session->getQuote();
    $address = $quote->getShippingAddress();
    $shippingMethod = $address->getShippingMethod();

    if ($session->getIpSelDate())
    {
        if (strpos($shippingMethod, '_') !== false)
        {
            $methodId = explode('_', $shippingMethod);
            $sessionId = $this->_quoteHelper->getSessionId();

            $id = $methodId[1] . '_' . $methodId[2];
            $sessionId = $this->_quoteHelper->getSessionId();
            $scheduledId = $session->getIpScheludedMethodId();

            if ($scheduledId == $id)
            {
/*
                $collection = $this->_quoteFactory->create()->getCollection();
                $collection->getSelect()->where("session_id = '{$sessionId}' AND delivery_method_id = '{$id}'");
*/
                $resultQuotes = $this->_quoteHelper->getResultQuotes();

                if (!empty($resultQuotes) && count($resultQuotes) > 0 /* $collection->count() */)
                {
                    $cookie = $this->_cookieManager->getCookie(\Intelipost\Quote\Controller\Schedule\Index::COOKIE_NAME);
                    if ($cookie)
                    {
                        $scheduled = explode('+', $cookie);

                        if ($scheduled[0] == $id)
                        {
                            $selDate = $scheduled[1];
                            $period  = $scheduled[2];
                        }
                        else
                        {
                            return null;
                        }
                    }
                    else
                    {

                        return null;
                    }
                }
                else
                {

                    $selDate = $session->getIpSelDate();
                    $period  = $session->getIpPeriod();

                    // $item = $collection->getFirstItem();

                    $item = null;

                    foreach ($resultQuotes as $quote)
                    {
                        if (!strcmp($quote->getDeliveryMethodId(), $id))
                        {
                            $item = $quote;

                            break;
                        }
                    }

                    if(empty($item)) return null;

                    $item->setSelectedSchedulingDates($selDate);
                    $item->setSelectedSchedulingPeriod($period);
                    // $item->save();
                }

                $resultPage = $this->_resultPageFactory->create();

                $this->getResponse()->setBody(
                    __('Delivery Scheduled for: %1 period: %2', $selDate, __(ucfirst($period)))
                );
            }
        }
    }
}

}

