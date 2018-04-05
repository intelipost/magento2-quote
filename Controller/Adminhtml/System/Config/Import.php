<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Controller\Adminhtml\System\Config;

class Import extends \Magento\Backend\App\Action
{

public function execute()
{
    $requestTable = $this->getRequest()->getParam('table');

    $this->_getImportSingleton()->import($requestTable);
}

protected function _getImportSingleton()
{
    return $this->_objectManager->get('Intelipost\Quote\Model\Import');
}

}

