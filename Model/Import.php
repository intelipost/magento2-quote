<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Import extends AbstractModel
{
    const FALLBACK_URL = 'https://raw.githubusercontent.com/intelipost/fallback-tables/master/';

    protected $_directoryList;
    public function __construct(
        Context $context,
        Registry $registry,
        DirectoryList $directoryList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_directoryList = $directoryList;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function import($tableName)
    {

        $varPath = $this->_directoryList->getPath(DirectoryList::VAR_DIR);

        $fileName = strpos($tableName, '.json') !== false ? $tableName : $tableName . '.json';
        $data = $this->curl_get_contents(self::FALLBACK_URL . $fileName);

        if ($data && strcmp($data, 'Not Found')) {
            $intelipostVarPath = $varPath . DIRECTORY_SEPARATOR . 'intelipost';
            if (!is_dir($intelipostVarPath)) {
                mkdir($intelipostVarPath, 0755, true);
            }

            $filePath = $intelipostVarPath . DIRECTORY_SEPARATOR . $fileName;
            file_put_contents($filePath, $data);

            if (strcmp($fileName, 'state_codification')) {
                $data = $this->curl_get_contents(self::FALLBACK_URL . 'state_codification.json');

                $filePath = $intelipostVarPath . DIRECTORY_SEPARATOR . 'state_codification.json';
                file_put_contents($filePath, $data);
            }
        }
    }

    public function curl_get_contents($url)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $data = curl_exec($curl);

        curl_close($curl);

        return $data;
    }
}
