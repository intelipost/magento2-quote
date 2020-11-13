<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Model\Config\Source\Attribute;

class Select extends \Intelipost\Quote\Model\Config\Source\Attribute
{
    public function toOptionArray()
    {
        $result = [
            ['value' => '', 'label' => __(' --- Please Select --- ')]
        ];

        return array_merge($result, parent::toOptionArray());
    }
}
