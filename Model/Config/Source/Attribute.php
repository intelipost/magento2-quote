<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Attribute implements OptionSourceInterface
{

public function toOptionArray()
{
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $collection = $objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')
        ->setAttributeSetFilter(4); // CATALOG
    $collection->getSelect()->order('frontend_label');

    $result = array ();

    foreach ($collection as $child)
    {
        $result [] = ['value' => $child->getAttributeCode(), 'label' => $child->getFrontendLabel ()];
    }

    return $result;
}

}

