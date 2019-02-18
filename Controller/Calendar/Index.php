<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Controller\Calendar;

class Index extends \Magento\Framework\App\Action\Action
{

protected $_helper;
protected $_quoteFactory;

public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    \Intelipost\Quote\Helper\Data $helper,
    \Intelipost\Quote\Model\QuoteFactory $quoteFactory
)
{
    $this->_helper = $helper;
    $this->_quoteFactory = $quoteFactory;
    $this->_resultPageFactory = $resultPageFactory;

    parent::__construct ($context);
}

public function execute()
{
    $info = $this->getRequest()->getParam('info');
    if(empty($info)) return false;

    $pieces = explode('_', $info); // carrier, method, store id
    $pieces = $pieces ? $pieces : [];
    
    if (count ($pieces) != 3) return false;

    $carrierName = $pieces [0];
    $deliveryMethodId = $pieces [1] . '_' . $pieces [2];
/*
    $sessionId = $this->_helper->getSessionId();

    $collection = $this->_quoteFactory->create()->getCollection();
    $collection->getSelect()->where(
        "session_id = '{$sessionId}' AND carrier = '{$carrierName}' AND delivery_method_id = '{$deliveryMethodId}'"
    );

    $item = $collection->getFirstItem();
    if (!$item->getId()) return false;
*/
    $item = null;

    foreach ($this->_helper->getResultQuotes() as $quote)
    {
        if(!strcmp($quote->getCarrier(), $carrierName) && !strcmp($quote->getDeliveryMethodId(), $deliveryMethodId))
        {
            $item = $quote;

            break;
        }
    }

    if (empty ($item)) return false;

    if (empty ($item->getAvailableSchedulingDates())) return false;

    $resultPage = $this->_resultPageFactory->create();
    $this->getResponse()->setBody(
        $resultPage->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setQuoteItem($item)
            ->setTemplate('Intelipost_Quote::calendar/result.phtml')
            ->toHtml()
    );
}

}

