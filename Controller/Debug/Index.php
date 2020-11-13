<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Controller\Debug;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_intelipostHelper;
    protected $_quoteFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Intelipost\Quote\Helper\Data $intelipostHelper,
        \Intelipost\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->_intelipostHelper = $intelipostHelper;
        $this->_quoteFactory = $quoteFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        /*
            $sessionId = $this->_intelipostHelper->getSessionId();

            $collection = $this->_quoteFactory->create()->getCollection();
            $collection->getSelect()->where("session_id = '{$sessionId}' AND order_id IS NULL");

            $quoteItems = $collection->toArray();
        */
        $quoteItems = ['items' => []];

        $resultPickup = $this->_intelipostHelper->getResultQuotes(\Intelipost\Quote\Helper\Data::RESULT_PICKUP);
        $resultQuotes = array_merge($this->_intelipostHelper->getResultQuotes(), $resultPickup);

        foreach ($resultQuotes as $quote) {
            $quoteItems ['items'][] = $quote->getData();
        }

        $qty = 0;
        foreach ($quoteItems ['items'] as $id => $item) {
            $quoteItems ['items'][$id]['api_request'] = json_decode($item ['api_request'], true);
            $quoteItems ['items'][$id]['api_response'] = json_decode($item ['api_response'], true);
            $quoteItems ['items'][$id]['products'] = json_decode($item ['products'], true);
            $qty++;
        }

        $this->getResponse()->setBody("<pre>qty:{$qty}\n" . print_r($quoteItems, true));
    }
}
