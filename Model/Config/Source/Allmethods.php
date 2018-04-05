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
	$result = array(
        array(
            'value' => 'none',
            'label' => __('None')
        ),
        array(
            'value' => 'lower_price',
            'label' => __('Lower Price'),
        ),
        /*
        array(
            'value' => 'lower_cost',
            'label' => __('Lower Cost'),
        ),
        */
        array(
            'value' => 'lower_delivery_date',
            'label' => __('Lower Delivery Date')
        ),
        /*
        array(
            'value' => 'greater_delivery_date',
            'label' => __('Greater Delivery Date')
        ),
        array(
            'value' => '1',
            'label' =>  'Correios PAC'
        ),
        array(
            'value' => '2',
            'label' => 'Correios Sedex'
        ),
        array(
            'value' => '3',
            'label' => 'Correios E-Sedex'
        ),
        array(
            'value' => '4',
            'label' => 'Total Express'
        ),
        array(
            'value' => '5',
            'label' => 'Loggi'
        ),
        array(
            'value' => '8',
            'label' => 'Direct E-Direct'
        ),
        array(
            'value' => '21',
            'label' => 'Vialog Express'
        ),
        array(
            'value' => '138',
            'label' => 'Motoboy Delivery'
        ),
        array(
            'value' => '10000',
            'label' => 'Premium Shipping'
        )
        */
    );

    return $result;
}

}

