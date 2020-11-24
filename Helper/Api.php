<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Helper;

class Api
{
    const QUOTE_BY_PRODUCT = 'quote_by_product/';
    const QUOTE_BUSINESS_DAYS = 'quote/business_days/';
    const QUOTE_AVAILABLE_SCHEDULING_DATES = 'quote/available_scheduling_dates/';

    protected $_scopeConfig;
    protected $client;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Intelipost\Basic\Client\Intelipost $client
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->client = $client;
    }

    public function quoteRequest($httpMethod, $apiMethod, &$postData = false)
    {
        $postData ['api_request'] = $postData;

        $response = $this->client->apiRequest($httpMethod, $apiMethod, $postData);

        $result = json_decode($response, true);

        if (!$result) {
            $result = $this->getContingencyValues($postData);

            $postData ['api_response'] = json_encode($result, true);

            return $result;
        }

        if (!strcmp($result ['status'], 'ERROR')) {
            throw new \Exception("Erro ao consultar API");
        }

        $postData ['api_response'] = $response;

        return $result;
    }

    public function getContingencyValues($postData)
    {
        $destZipcode = intval($postData ['destination_zip_code']);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dir = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        $varPath = $dir->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);

        $intelipostVarPath = $varPath . DIRECTORY_SEPARATOR . 'intelipost';

        /*
         * calculate State Codification
         */
        $stateFile = $intelipostVarPath . DIRECTORY_SEPARATOR . 'state_codification.json';
        $stateContent = json_decode(file_get_contents($stateFile), true);

        $stateCode = null;
        $stateType = null;

        foreach ($stateContent [0] as $beginZip => $child) {
            if ($destZipcode >= $beginZip && $destZipcode <= $child ['cep_end']) {
                $stateCode = strtoupper($child ['state']);
                $stateType = strtoupper($child ['type']);

                break;
            }
        }

        /*
         * calculate Contingency Table
         */
        $contingencyTable = $this->_scopeConfig->getValue('carriers/intelipost/contingency_table');
        $tableFile = $intelipostVarPath . DIRECTORY_SEPARATOR . $contingencyTable;

        $tableContent = json_decode(file_get_contents($tableFile), true);

        $totalWeight = 0;
        foreach ($postData ['products'] as $product) {
            $totalWeight += intval($product ['weight']);
        }

        $delivery = [
            'delivery_method_id' => 'fallback',
            'delivery_method_type' => 'fallback',
            'provider_shipping_cost' => 0,
        ];

        foreach ($tableContent as $stateId => $stateContent) {
            if (!strcmp(strtoupper($stateId), $stateCode)) {
                foreach ($stateContent as $regionId => $regionContent) {
                    if (!strcmp(strtoupper($regionId), $stateType)) {
                        $delivery ['delivery_estimate_business_days'] = $regionContent ['delivery_estimate_business_days'];

                        foreach ($regionContent ['final_shipping_cost'] as $weight => $price) {
                            if ($totalWeight <= $weight) {
                                $delivery ['final_shipping_cost'] = $price;
                                $delivery ['logistic_provider_name'] = $regionId;
                                $delivery ['description'] = $regionId;
                                $delivery ['delivery_method_name'] = $regionId;

                                break;
                            }
                        }
                    }
                }
            }
        }

        $result = [
            'content' => [
                'id' => 0,
                'delivery_options' => [$delivery]
            ]
        ];

        return $result;
    }

    public function getCache($postData)
    {
        $identifier = $this->getCacheIdentifier($postData);

        $result = unserialize($this->_cache->load($identifier));

        return $result;
    }

    public function getCacheIdentifier($postData)
    {
        $identifier = 'intelipost_api_'
            . $postData ['destination_zip_code'] . '_'
            . $postData ['cart_weight'] . '_'
            . $postData ['cart_amount'] . '_'
            . $postData ['cart_qtys'];

        return $identifier;
    }

    public function getEstimateDeliveryDate($originZipcode, $destPostcode, $businessDays)
    {
        $response = $this->apiRequest(self::GET, self::QUOTE_BUSINESS_DAYS
            . "{$originZipcode}/{$destPostcode}/{$businessDays}");

        $result = json_decode($response, true);

        return $result;
    }

    public function getAvailableSchedulingDates($originZipcode, $destPostcode, $deliveryMethodId)
    {
        $response = $this->apiRequest(self::GET, self::QUOTE_AVAILABLE_SCHEDULING_DATES
            . "{$deliveryMethodId}/{$originZipcode}/{$destPostcode}");

        $result = json_decode($response, true);

        return $result;
    }
}
