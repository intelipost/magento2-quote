<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Model\Resource\Quote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            'Intelipost\Quote\Model\Quote',
            'Intelipost\Quote\Model\Resource\Quote'
        );
    }

}

