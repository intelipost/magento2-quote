<?php

namespace Intelipost\Quote\Model\Quote\Address;

class Plugin
{
    protected $_scopeConfig;

	public function aroundImportShippingRate($subject, $proceed, \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult $rate)
	{
        $result = $proceed($rate);

        $warnMessage = $rate->getWarnMessage();
        if (!empty ($warnMessage))
        {
            $result->setErrorMessage($warnMessage);
        }

        return $result;
	}
}

