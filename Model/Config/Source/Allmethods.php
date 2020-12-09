<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Model\Config\Source;

class Allmethods implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'none',
                'label' => __('None')
            ],
            [
                'value' => 'lower_price',
                'label' => __('Lower Price'),
            ],
            [
                'value' => 'lower_delivery_date',
                'label' => __('Lower Delivery Date')
            ],
        ];
    }
}
