<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Block;

class Calendar extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->setTemplate('calendar.phtml');

        parent::__construct($context);
    }

    public function getAjaxCalendarUrl()
    {
        return $this->getUrl('intelipost_quote/calendar/index');
    }

    public function getAjaxScheduleUrl()
    {
        return $this->getUrl('intelipost_quote/schedule/index');
    }

    public function getAjaxScheduleStatusUrl()
    {
        return $this->getUrl('intelipost_quote/schedule/status');
    }
}
