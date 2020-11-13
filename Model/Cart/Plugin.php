<?php

namespace Intelipost\Quote\Model\Cart;

class Plugin
{
    public function aroundModelToDataObject($subject, $proceed, \Magento\Quote\Model\Quote\Address\Rate $rateModel, $quoteCurrencyCode)
    {
        $result = $proceed($rateModel, $quoteCurrencyCode);

        $warnMessage = $rateModel->getWarnMessage();
        if (!empty($warnMessage)) {
            $result->setErrorMessage($warnMessage);
        }

        return $result;
    }
}

