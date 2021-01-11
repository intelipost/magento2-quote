<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Unit implements OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'gr', 'label' => __('Gram')],
            ['value' => 'kg', 'label' => __('Kilo')]
        ];
    }
}
