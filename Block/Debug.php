<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Block;

use Magento\Framework\View\Element\Template\Context;

class Debug extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);

        if ($this->_scopeConfig->getValue('carriers/intelipost/debug')) {
            $this->setTemplate('debug.phtml');
        }
    }

    public function getAjaxDebugUrl()
    {
        return $this->getUrl('intelipost_quote/debug/index');
    }
}
