<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const RESULT_QUOTES = 'intelipost_result_quotes';
    const RESULT_PICKUP = 'intelipost_result_pickup';

    protected $_quoteFactory;
    protected $_sessionManager;
    protected $_categoryCollectionFactory;
    protected $_backendSession;
    protected $_checkoutSession;
    protected $_customerSession;
    protected $_customerGroup;
    protected $_storeManager;
    protected $_moduleManager;
    protected $_categoryRepository;
    protected $_resourceConnection;
    protected $_session;
    protected $_state;

    protected $_intelipostQuote;
    protected $_selectedSchedulingMethod;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Intelipost\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Backend\Model\Session\Quote $backendSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Group $customerGroup,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Intelipost\Quote\Model\Quote $intelipostQuote,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\State $state
    ) {
        $this->_quoteFactory = $quoteFactory;
        $this->_sessionManager = $sessionManager;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_backendSession = $backendSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_customerGroup = $customerGroup;
        $this->_storeManager = $storeManager;
        $this->_categoryRepository = $categoryRepository;
        $this->_resourceConnection = $resourceConnection;
        $this->_session = $session;
        $this->_state = $state;

        $this->_intelipostQuote = $intelipostQuote;
        $this->_selectedSchedulingMethod = [];

        parent::__construct($context);
    }

    public function isModuleEnabled($moduleName = '')
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }

    public function getCustomCarrierTitle($carrier = 'intelipost', $description, $estimated_delivery, $scheduled = false)
    {
        if ($scheduled) {
            return $this->scopeConfig->getValue("carriers/intelipost/scheduled_title");
        } else {
            if (!$estimated_delivery) {
                return sprintf($this->scopeConfig->getValue("carriers/{$carrier}/same_day_title"), $description);
            } else {
                return sprintf($this->scopeConfig->getValue("carriers/{$carrier}/custom_title"), $description, $estimated_delivery);
            }
        }
    }

    public function getProductCategories($product, $contingency = false)
    {

        $categories = null;
        $result = null;
        $collection = $product->getCategoryCollection();

        foreach ($collection as $child) {
            $category = $this->_categoryRepository->getById($child->getId());
            $categories [] = $category->getName();
        }

        if (empty($categories)) {
            $categoriesContingency = $this->scopeConfig->getValue('carriers/intelipost/categories_contingency');

            if ($contingency && $categoriesContingency) {
                $collection = $this->_categoryCollectionFactory->create();
                $collection->addAttributeToSelect('name');
                $collection->addAttributeToFilter('entity_id', ['in', $categoriesContingency]);

                if ($collection->count()) {
                    foreach ($collection as $category) {
                        $categories [] = $category->getName();
                    }

                    $result = implode(',', $categories);
                }
            }
        }

        return $result;
    }

    public function removeQuotes($carrier = '')
    {
        $originalPathInfo = $this->_request->getOriginalPathInfo();
        if (strpos($originalPathInfo, '/shipping-information') !== false
            || strpos($originalPathInfo, '/payment-information') !== false) {
            return false;
        } // keep on checkout payment

        $connection = $this->_resourceConnection->getConnection();
        $tableName = $this->_resourceConnection->getTableName('intelipost_quote'); //gives table name with prefix

        $sessionId = $this->getSessionId();

        $this->checkSelected();

        $sql = <<< SQL
DELETE FROM {$tableName}
WHERE session_id = '{$sessionId}'
AND order_id IS NULL
AND shipping_method IS NULL
AND carrier = '{$carrier}'
SQL;
        $connection->query($sql);

        return true;
    }

    public function getSessionId()
    {
        $result = $this->_sessionManager->getSessionId();

        return $result;
    }

    public function checkSelected()
    {
        $sessionId = $this->getSessionId();
        /*
            $collection = $this->_intelipostQuote->getCollection();
            $collection->getSelect()->where("session_id = '{$sessionId}' AND carrier = 'presales'");
        */
        $resultQuotes = $this->getResultQuotes();
        if (!empty($resultQuotes) && count($resultQuotes) > 0 /* $collection->count() */) {
            foreach ($resultQuotes as $item) {
                if ($item->getSelectedSchedulingDates()) {
                    $this->_selectedSchedulingMethod['delivery_method_id'] = $item->getDeliveryMethodId();
                    $this->_selectedSchedulingMethod['selected_scheduling_dates'] = $item->getSelectedSchedulingDates();
                    $this->_selectedSchedulingMethod['selected_scheduling_period'] = $item->getSelectedSchedulingPeriod();

                    return;
                }
            }
        }

        return false;
    }

    public function getResultQuotes($key = self::RESULT_QUOTES)
    {
        $result = $this->_session->getData($key);

        return !empty($result) ? $result : [];
    }

    public function savePickupQuote($carrier, $itemId, $storeId)
    {
        $quote = $this->_quoteFactory->create();

        $sessionId = $this->getSessionId();

        $quote->setSessionId($sessionId);
        $quote->setCarrier($carrier);
        $quote->setQuoteId($itemId);
        $quote->setDeliveryMethodId($storeId);

        $quote->setApiRequest(null);
        $quote->setApiResponse(null);
        $quote->setProducts(null);

        // $quote->save();

        return $quote;
    }

    public function saveQuoteOld($carrier, $id, $method, $postData)
    {
        $originalPathInfo = $this->_request->getOriginalPathInfo();
        if (strpos($originalPathInfo, '/shipping-information') !== false
            || strpos($originalPathInfo, '/payment-information') !== false) {
            return false;
        } // keep on checkout payment

        $quote = $this->_quoteFactory->create();

        $sessionId = $this->getSessionId();

        $quote->setSessionId($sessionId);
        $quote->setCarrier($carrier);
        $quote->setQuoteId($id);
        $quote->setProducts(json_encode($postData ['products']));

        $quote->setLogisticProviderName($method ['logistic_provider_name']);
        $quote->setDescription($method ['description']);

        $quote->setDeliveryMethodId($method ['delivery_method_id']);
        $quote->setDeliveryEstimateBusinessDays($method ['delivery_estimate_business_days']);

        $quote->setAvailableSchedulingDates($method ['available_scheduling_dates']);

        $quote->setProviderShippingCost($method ['provider_shipping_cost']);
        $quote->setFinalShippingCost($method ['final_shipping_cost']);

        $quote->setApiRequest($postData ['api_request']);
        $quote->setApiResponse($postData ['api_response']);

        // $quote->save ();

        return $quote;
    }

    public function saveQuote($carrier, $id, $method, $postData, $volumes = false)
    {
        /*
        $originalPathInfo = $this->_request->getOriginalPathInfo();
        if (strpos ($originalPathInfo, '/shipping-information') !== false
            || strpos ($originalPathInfo, '/payment-information') !== false
            || strpos ($originalPathInfo, 'payment') !== false) return false; // keep on checkout payment
        */
        $sessionId = $this->getSessionId();
        /*
        $quoteColl = $this->_intelipostQuote->getCollection();
        $quoteColl->getSelect()->where("session_id = '{$sessionId}'");
        */

        $quoteColl = $this->getResultQuotes();

        $data_exists = false;

        if (/* disabled */ false && !empty($quoteColl) && count($quoteColl) > 0 /* $quoteColl->count() */) {
            foreach ($quoteColl as $single_data) {
                if ($single_data['delivery_method_id'] == $method ['delivery_method_id']) {
                    $data_exists = true;

                    //$single_data->setSessionId ($sessionId);
                    $single_data->setCarrier($carrier);
                    $single_data->setQuoteId($id);
                    $single_data->setProducts(json_encode($postData ['products']));

                    $single_data->setLogisticProviderName($method ['logistic_provider_name']);
                    $single_data->setDescription($method ['description']);

                    //$single_data->setDeliveryMethodId ($method ['delivery_method_id']);
                    $single_data->setDeliveryEstimateBusinessDays($method ['delivery_estimate_business_days']);

                    if (array_key_exists('delivery_estimate_date_exact_iso', $method)) {
                        $single_data->setDeliveryExactEstimatedDate($method['delivery_estimate_date_exact_iso']);
                    }

                    $single_data->setDeliveryMethodName($method['delivery_method_name']);

                    if (array_key_exists('delivery_method_type', $method)) {
                        $single_data->setDeliveryMethodType($method['delivery_method_type']);
                    }

                    $single_data->setAvailableSchedulingDates($method ['available_scheduling_dates']);

                    $single_data->setProviderShippingCost($method ['provider_shipping_cost']);
                    $single_data->setFinalShippingCost($method ['final_shipping_cost']);

                    $single_data->setApiRequest($postData ['api_request']);
                    $single_data->setApiResponse($postData ['api_response']);

                    if (!empty($this->_selectedSchedulingMethod)) {
                        if ($method['delivery_method_id'] == $this->_selectedSchedulingMethod['delivery_method_id']) {
                            $single_data->setSelectedSchedulingDates($this->_selectedSchedulingMethod['selected_scheduling_dates']);
                            $single_data->setSelectedSchedulingPeriod($this->_selectedSchedulingMethod['selected_scheduling_period']);
                        }
                    }
                }
            }

            // $single_data->save();
        }

        if (!$data_exists) {
            $quote = $this->_quoteFactory->create();

            $quote->setSessionId($sessionId);
            $quote->setCarrier($carrier);
            $quote->setQuoteId($id);
            $quote->setProducts(json_encode($postData ['products']));
            $quote->setOriginZipCode($postData ['origin_zip_code']);

            $quote->setLogisticProviderName($method ['logistic_provider_name']);
            $quote->setDescription($method ['description']);

            $quote->setDeliveryMethodId($method ['delivery_method_id']);
            $quote->setDeliveryEstimateBusinessDays($method ['delivery_estimate_business_days']);

            if (array_key_exists('delivery_estimate_date_exact_iso', $method)) {
                $quote->setDeliveryExactEstimatedDate($method['delivery_estimate_date_exact_iso']);
            }

            $quote->setDeliveryMethodName($method['delivery_method_name']);

            if (array_key_exists('delivery_method_type', $method)) {
                $quote->setDeliveryMethodType($method['delivery_method_type']);
            }

            $quote->setAvailableSchedulingDates($method ['available_scheduling_dates']);

            $quote->setProviderShippingCost($method ['provider_shipping_cost']);
            $quote->setFinalShippingCost($method ['final_shipping_cost']);

            $quote->setApiRequest($postData ['api_request']);
            $quote->setApiResponse($postData ['api_response']);

            if (!empty($this->_selectedSchedulingMethod)) {
                if ($method['delivery_method_id'] == $this->_selectedSchedulingMethod['delivery_method_id']) {
                    $quote->setSelectedSchedulingDates($this->_selectedSchedulingMethod['selected_scheduling_dates']);
                    $quote->setSelectedSchedulingPeriod($this->_selectedSchedulingMethod['selected_scheduling_period']);
                }
            }

            $quote->setQuoteVolume($volumes);

            // $quote->save ();

            return $quote;
        }

        /*
        $quote = $this->_quoteFactory->create();

        $sessionId = $this->getSessionId ();

        $quote->setSessionId ($sessionId);
        $quote->setCarrier ($carrier);
        $quote->setQuoteId ($id);
        $quote->setProducts (json_encode ($postData ['products']));

        $quote->setLogisticProviderName ($method ['logistic_provider_name']);
        $quote->setDescription ($method ['description']);

        $quote->setDeliveryMethodId ($method ['delivery_method_id']);
        $quote->setDeliveryEstimateBusinessDays ($method ['delivery_estimate_business_days']);

        if (array_key_exists('delivery_estimate_date_exact_iso', $method)) {
            $quote->setDeliveryExactEstimatedDate($method['delivery_estimate_date_exact_iso']);
        }

        $quote->setDeliveryMethodName($method['delivery_method_name']);

        if (array_key_exists('delivery_method_type', $method)) {
            $quote->setDeliveryMethodType($method['delivery_method_type']);
        }

        $quote->setAvailableSchedulingDates ($method ['available_scheduling_dates']);

        $quote->setProviderShippingCost ($method ['provider_shipping_cost']);
        $quote->setFinalShippingCost ($method ['final_shipping_cost']);

        $quote->setApiRequest  ($postData ['api_request']);
        $quote->setApiResponse ($postData ['api_response']);

        if (!empty($this->_selectedSchedulingMethod))
        {
            if ($method['delivery_method_id'] == $this->_selectedSchedulingMethod['delivery_method_id'])
            {
                $quote->setSelectedSchedulingDates($this->_selectedSchedulingMethod['selected_scheduling_dates']);
                $quote->setSelectedSchedulingPeriod($this->_selectedSchedulingMethod['selected_scheduling_period']);
            }
        }
        */
        //$selected = $this->checkSelected() ? $this->checkSelected() : false;
        //$quote->save ();

        return null;
    }

    public function saveResultQuotes(array $data = null, $removeOlds = true, $key = self::RESULT_QUOTES)
    {
        if ($removeOlds) {
            $this->_session->setData($key, $data);
        } else {
            $quotes = $this->_session->getData($key);

            $result = $quotes ? $quotes : [];

            $this->_session->setData($key, array_merge($result, $data));
        }
    }

    public function checkFreeshipping(&$response)
    {
        $freeshippingMethod = $this->scopeConfig->getValue('carriers/intelipost/freeshipping_method');
        $freeshippingText = $this->scopeConfig->getValue('carriers/intelipost/freeshipping_text');

        $lowerPrice = PHP_INT_MAX;
        $lowerDeliveryDate = PHP_INT_MAX;
        $lowerMethod = null;

        foreach ($response ['content']['delivery_options'] as $child) {
            $deliveryMethodId = $child ['delivery_method_id'];
            $finalShippingCost = $child ['final_shipping_cost'];
            $deliveryEstimateDays = $child ['delivery_estimate_business_days'];

            switch ($freeshippingMethod) {
                case 'lower_price':
                {
                    if ($finalShippingCost < $lowerPrice) {
                        $lowerPrice = $finalShippingCost;
                        $lowerMethod = $deliveryMethodId;
                    }

                    break;
                }
                case 'lower_delivery_date':
                {
                    if ($deliveryEstimateDays < $lowerDeliveryDate) {
                        $lowerDeliveryDate = $DeliveryEstimateDays;
                        $lowerMethod = $deliveryMethodId;
                    }

                    break;
                }
            }
        }

        foreach ($response ['content']['delivery_options'] as $id => $child) {
            $deliveryMethodId = $child ['delivery_method_id'];
            if (!strcmp($deliveryMethodId, $lowerMethod)) {
                $response ['content']['delivery_options'][$id]['final_shipping_cost'] = 0;
                $response ['content']['delivery_options'][$id]['description'] = $freeshippingText;

                break;
            }
        }
    }

    public function haveSpecialPrice($product)
    {
        $specialPrice = $product->getSpecialPrice();
        $specialFromDate = $product->getSpecialFromDate();
        $specialToDate = $product->getSpecialToDate();

        if (($specialPrice && $specialFromDate && !$specialToDate)
            || ($specialPrice && !$specialFromDate && !$specialToDate)) {
            return true;
        }

        if ($specialPrice && $specialFromDate && $specialToDate) {
            $timestamp = strtotime($specialToDate);
            $upToDate = date('Y-m-d', $timestamp);
            $now = date('Y-m-d');

            if ($upToDate >= $now) {
                return true;
            }
        }
    }

    public function getDiscountAmount()
    {
        return ($this->getQuote()->getBaseSubtotal() - $this->getQuote()->getBaseSubtotalWithDiscount()) * -1;
    }

    public function getQuote()
    {
        if ($this->isAdmin()) {
            $quote = $this->_backendSession->getQuote();
        } else {
            $quote = $this->_checkoutSession->getQuote();
        }

        return $quote;
    }

    public function isAdmin()
    {
        $areaCode = $this->_state->getAreaCode();
        return $this->_state->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }

    public function getSubtotalAmount($contingencyPrice = 0)
    {
        $result = $this->getQuote()->getBaseSubtotal();
        if (intval($result) > 0) {
            return $result;
        }

        return $contingencyPrice;
    }

    public function getAdditionalInformation(array $additional = null)
    {
        $result = [
            'client_type' => $this->getCustomerGroup(),
            'sales_channel' => $this->getStoreName(),
            'shipped_date' => $this->getShippedDate(),
        ];

        $additionCount = (is_array($additional) ? $additional : []);

        if (count($additionCount) > 0) {
            $result = array_merge($result, $additional);
        }

        return $result;
    }

    public function getCustomerGroup()
    {
        if ($this->isAdmin()) {
            $roleId = $this->_backendSession->getQuote()->getCustomerGroupId();
        } else {
            $roleId = $this->_customerSession->getCustomerGroupId();
        }

        $role = $this->_customerGroup->load($roleId)->getCustomerGroupCode();
        $role = strtolower($role);

        return $role;
    }

    public function getStoreName()
    {
        $result = $this->_storeManager->getStore()->getName();

        return $result;
    }

    public function getShippedDate($convert = true)
    {
        $aditionalDeliveryDate = intval($this->scopeConfig->getValue('carriers/intelipost/additional_delivery_date'));
        $moreDays = '+' . intval($aditionalDeliveryDate) . ' days';

        $timestamp = time();
        $timestamp = strtotime($moreDays, $timestamp);

        $wday = date('N', $timestamp);
        if ($wday >= 6) {
            $timestamp += (2 * 3600 * 24);
        }

        if (!$convert) {
            return $timestamp;
        }

        $result = date('Y-m-d', $timestamp);

        return $result;
    }

    public function getPageIdentification()
    {
        $result = [
            'ip' => $_SERVER ['REMOTE_ADDR'],
            'session' => $this->getSessionId(),
            'page_name' => $this->getPageName(),
            'url' => $this->getCurrentUrl()
        ];

        return $result;
    }

    public function getPageName()
    {
        $result = 'checkout';

        if ($this->isAdmin()) {
            $result = 'admin';
        } else {
            $originalPathInfo = $this->_request->getOriginalPathInfo();

            if (!strcmp($originalPathInfo, '/intelipost_quote/product/shipping/')) {
                $result = 'product';
            }
            //if (strpos ($originalPathInfo, '/estimate-shipping-methods') !== false) $result = 'cart';
            //if (strpos ($originalPathInfo, '/estimate-shipping-methods-by-address-id') !== false) $result = 'checkout';
        }

        return $result;
    }

    public function getCurrentUrl()
    {
        $url = $this->_storeManager->getStore()->getCurrentUrl();

        $result = urldecode(htmlspecialchars_decode($url));

        return $result;
    }

    public function haveData()
    {
        $args = func_get_args();
        $result = null;

        foreach ($args as $_arg) {
            if (!empty($_arg)) {
                $result = $_arg;

                break;
            }
        }

        return $result;
    }
}
