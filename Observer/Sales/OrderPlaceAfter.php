<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface
{
    protected $_intelipostQuote;
    protected $_intelipostHelper;
    protected $_sessionManager;
    protected $_cookieManager;
    protected $_shipmentFactory;
    protected $_storeManager;

    public function __construct(
        \Intelipost\Quote\Model\Quote $intelipostQuote,
        \Intelipost\Quote\Helper\Data $intelipostHelper,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Intelipost\Quote\Model\ShipmentFactory $shipmentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_intelipostQuote = $intelipostQuote;
        $this->_intelipostHelper = $intelipostHelper;
        $this->_sessionManager = $sessionManager;
        $this->_cookieManager = $cookieManager;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_storeManager = $storeManager;
    }

    public function executeOld(Observer $observer)
    {
        /*
            $sessionId = $this->_sessionManager->getSessionId ();

            $intelipostQuoteCollection = $this->_intelipostQuote->getCollection();
            $intelipostQuoteCollection->getSelect()->where("session_id = '{$sessionId}' AND order_id IS NULL");

            if (!$intelipostQuoteCollection->count()) return;
        */
        $orderInstance = $observer->getOrder();
        $result = null;

        foreach ($this->_intelipostHelper->getResultQuotes() as $quote) {
            $result [] = $quote->getData();
        }
        /*
            foreach ($intelipostQuoteCollection as $quoteItem)
            {
                $intelipostQuote = $this->_intelipostQuote->load ($quoteItem->getId ())
                    ->setOrderId($orderInstance->getIncrementId())
                    ->setShippingMethod($orderInstance->getShippingMethod())
                    ->save();

                $result [] = $intelipostQuote->getData();
            }
        */
        $orderInstance->setIntelipostQuote(
            json_encode($result)
        )->save();
    }

    public function execute(Observer $observer)
    {
        $sessionId = $this->_sessionManager->getSessionId();
        $orderInstance = $observer->getOrder();

        $resultQuotes = [];

        if (strpos($orderInstance->getShippingMethod(), '_') !== false) {
            $deliveryMethodId = explode("_", $orderInstance->getShippingMethod());
            if (count($deliveryMethodId) < 3) {
                return;
            }

            $deliveryMethodId = $deliveryMethodId[count($deliveryMethodId) - 2] . "_" . $deliveryMethodId[count($deliveryMethodId) - 1];
            /*
                    $intelipostQuoteCollection = $this->_intelipostQuote->getCollection();
                    $intelipostQuoteCollection->getSelect()->where("session_id = '{$sessionId}' AND delivery_method_id = '{$deliveryMethodId}' AND order_id IS NULL");

                    if (!$intelipostQuoteCollection->count()) return;
            */
            foreach ($this->_intelipostHelper->getResultQuotes() as $quote) {
                if ($quote->getDeliveryMethodId() == $deliveryMethodId && $quote->getOrderId() == null) {
                    $resultQuotes [] = $quote;
                }
            }

            if (empty($resultQuotes) && count($resultQuotes) == 0) {
                return;
            }
        }

        $result = null;
        $stored = [];
        $resultJson = [];
        /*
        $methodType = null;
        foreach ($intelipostQuoteCollection as $quoteItem)
        {
            $methodType = $quoteItem->getDeliveryMethodType();
            break;
        }

        $quoteColl = $this->_intelipostQuote->getCollection();
        $quoteColl->getSelect()->where("session_id = '{$sessionId}' AND delivery_method_type = '{$methodType}' AND order_id IS NULL");

        if (!$quoteColl->count()) return;
        */

        $cookie = $this->_cookieManager->getCookie(\Intelipost\Quote\Controller\Schedule\Index::COOKIE_NAME);

        foreach ($resultQuotes as $quoteItem) {
            if (in_array($quoteItem->getQuoteId(), $stored)) {
                continue;
            }
            /*
                    $intelipostQuote = $this->_intelipostQuote->load ($quoteItem->getId ())
                        ->setOrderId($orderInstance->getIncrementId())
                        ->setShippingMethod($orderInstance->getShippingMethod())
                        ->save();
            */
            $quotes = [];

            if ($cookie) {
                $scheduled = explode('+', $cookie);

                if ($scheduled[0] == $quoteItem->getDeliveryMethodId()) {
                    if (!$quoteItem->getSelectedSchedulingDates() || !$quoteItem->getSelectedSchedulingPeriod()) {
                        $quoteItem->setSelectedSchedulingDates($scheduled[1]);
                        $quoteItem->setSelectedSchedulingPeriod($scheduled[2]);
                    }
                }
            }

            if (count($resultJson) == 0) {
                $quotes[] = [
                    'quote_id' => $quoteItem->getQuoteId(),
                    'final_shipping_cost' => $quoteItem->getFinalShippingCost(),
                    'provider_shipping_cost' => $quoteItem->getProviderShippingCost(),
                    'delivery_exact_estimated_date' => $quoteItem->getDeliveryExactEstimatedDate(),
                    'delivery_estimated_delivery_business_day' => $quoteItem->getDeliveryEstimateBusinessDays(),
                    'delivery_method_type' => $quoteItem->getDeliveryMethodType(),
                    'products' => $quoteItem->getProducts(),
                    'description' => $quoteItem->getDescription(),
                    'delivery_method_name' => $quoteItem->getDeliveryMethodName(),
                    'quote_volume' => $quoteItem->getQuoteVolume(),
                    'origin_zip_code' => $quoteItem->getOriginZipCode(),
                    'delivery_method_id' => $quoteItem->getDeliveryMethodId(),
                    'selected_scheduling_dates' => $quoteItem->getSelectedSchedulingDates(),
                    'selected_scheduling_period' => $quoteItem->getSelectedSchedulingPeriod(),
                    'intelipost_status' => 'pending'
                ];

                $resultJson ['result'] = [
                    'session_id' => $quoteItem->getSessionId(),
                    'shipping_method' => $orderInstance->getShippingMethod(),
                    'delivery_method_id' => $quoteItem->getDeliveryMethodId(),
                    'total_final_shipping_cost' => $quoteItem->getFinalShippingCost(),
                    'total_provider_shipping_cost' => $quoteItem->getProviderShippingCost(),
                    'order_id' => $orderInstance->getIncrementId(),
                    'logistic_provider_name' => $quoteItem->getLogisticProviderName(),
                    'delivery_method_name' => $quoteItem->getDeliveryMethodName(),
                    'selected_scheduling_dates' => $quoteItem->getSelectedSchedulingDates(),
                    'selected_scheduling_period' => $quoteItem->getSelectedSchedulingPeriod(),
                    'quotes' => $quotes
                ];
            } else {
                $quotes = [
                    'quote_id' => $quoteItem->getQuoteId(),
                    'final_shipping_cost' => $quoteItem->getFinalShippingCost(),
                    'provider_shipping_cost' => $quoteItem->getProviderShippingCost(),
                    'delivery_exact_estimated_date' => $quoteItem->getDeliveryExactEstimatedDate(),
                    'delivery_estimated_delivery_business_day' => $quoteItem->getDeliveryEstimateBusinessDays(),
                    'origin_zip_code' => $quoteItem->getOriginZipCode(),
                    'delivery_method_type' => $quoteItem->getDeliveryMethodType(),
                    'products' => $quoteItem->getProducts(),
                    'description' => $quoteItem->getDescription(),
                    'delivery_method_name' => $quoteItem->getDeliveryMethodName(),
                    'quote_volume' => $quoteItem->getQuoteVolume(),
                    'delivery_method_id' => $quoteItem->getDeliveryMethodId(),
                    'selected_scheduling_dates' => $quoteItem->getSelectedSchedulingDates(),
                    'selected_scheduling_period' => $quoteItem->getSelectedSchedulingPeriod(),
                    'intelipost_status' => 'pending'
                ];

                $resultJson ['result']['total_final_shipping_cost'] += $quoteItem->getFinalShippingCost();
                $resultJson ['result']['total_provider_shipping_cost'] += $quoteItem->getProviderShippingCost();

                array_push($resultJson['result']['quotes'], $quotes);
            }

            //$result [] = $intelipostQuote->getData();

            $stored[$quoteItem->getQuoteId()] = $quoteItem->getQuoteId();
        }

        if ($resultJson) {
            $this->setShipmentOrder($resultJson);
        }

        $resultEncode = json_encode($resultJson);

        $orderInstance->setIntelipostQuote($resultEncode)->save();
    }

    public function setShipmentOrder($_resultJson)
    {
        $orderIndex = 1;
        $order_number = $_resultJson['result']['order_id'];

        foreach ($_resultJson['result']['quotes'] as $quotes) {
            $obj = $this->_shipmentFactory->create();
            $obj->setOrderNumber($order_number);
            if ($orderIndex != 1) {
                $obj->setOrderNumber($order_number . '-' . $orderIndex);
            }

            $obj->setQuoteId($quotes['quote_id']);
            $obj->setDeliveryMethodId($this->getMethodId($quotes['delivery_method_id']));
            $obj->setDeliveryEstimateBusinessDays($quotes['delivery_estimated_delivery_business_day']);
            $obj->setShipmentOrderType('NORMAL');
            $obj->setShipmentOrderSubType('NORMAL');
            $obj->setDeliveryMethodType($quotes['delivery_method_type']);
            $obj->setDeliveryMethodName($quotes['delivery_method_name']);
            $obj->setDescription($quotes['description']);
            $obj->setSalesChannel($this->_storeManager->getStore()->getName());
            $obj->setProviderShippingCosts($quotes['provider_shipping_cost']);
            $obj->setCustomerShippingCosts($quotes['final_shipping_cost']);
            $obj->setIntelipostStatus('pending');
            $obj->setVolumes(json_encode($quotes['quote_volume']));
            $obj->setDeliveryEstimateDateExactIso($quotes['delivery_exact_estimated_date']);
            $obj->setScheduled(false);
            $obj->setProductsIds(json_encode($this->setProductsArray($quotes['products'])));
            $obj->setOriginZipCode($quotes['origin_zip_code']);

            if ($quotes['selected_scheduling_dates']) {
                $obj->setScheduled(true);
                $time = $this->schedulingTime($quoteItem->getSelectedSchedulingPeriod());
                $obj->setSchedulingWindowStart($time->start);
                $obj->setSchedulingWindowEnd($time->end);
                $obj->setSelectedSchedulingDate($quotes['selected_scheduling_dates']);
            }

            $orderIndex++;

            $obj->save();
        }
    }

    public function getMethodId($method_id)
    {
        preg_match_all('!\d+!', $method_id, $matches);
        foreach ($matches as $key => $value) {
            $id = ($value) ? (int)$value[0] : 100;
        }
        return $id;
    }

    public function setProductsArray($products)
    {
        $productsObj = json_decode($products);
        $productsArray = [];

        foreach ($productsObj as $prod) {
            array_push($productsArray, $prod->id);
        }
        return $productsArray;
    }

    public function schedulingTime($period)
    {
        $schedulingTime = new \stdClass();
        switch ($period) {
            case "morning":
                $schedulingTime->start = '08:00:00';
                $schedulingTime->end = '12:00:00';
                return $schedulingTime;
            case "afternoon":
                $schedulingTime->start = '12:00:00';
                $schedulingTime->end = '18:00:00';
                return $schedulingTime;
            default:
                $schedulingTime->start = '08:00:00';
                $schedulingTime->end = '18:00:00';
                return $schedulingTime;
        }
    }
}
