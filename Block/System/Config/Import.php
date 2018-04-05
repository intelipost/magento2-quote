<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Block\System\Config;

class Import extends \Magento\Config\Block\System\Config\Form\Field
{

protected $_template = 'Intelipost_Quote::system/config/import.phtml';

public function __construct(
    \Magento\Backend\Block\Template\Context $context,
    array $data = []
)
{
    parent::__construct($context, $data);
}

public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
{
    $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

    return parent::render($element);
}

protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
{
    return $this->_toHtml();
}

public function getAjaxContingencyImportUrl()
{
    return $this->getUrl('intelipost/system_config/import');
}

public function getButtonHtml()
{
    $button = $this->getLayout()->createBlock(
        'Magento\Backend\Block\Widget\Button'
    )
    ->setData(
        [
            'id' => 'contingency_import',
            'label' => __('Import'),
        ]
    );

    return $button->toHtml();
}

}

