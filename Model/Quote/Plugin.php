<?php

namespace Intelipost\Quote\Model\Quote;

class Plugin
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    public function afterGetGroupedAllShippingRates($subject, $result)
    {
        $scheduledIndex = 0;
        $scheduled = null;

        $ag_title = $this->_scopeConfig->getValue("carriers/intelipost/scheduled_title");
        $ag_last = $this->_scopeConfig->getValue("carriers/intelipost/scheduled_last");

        if (!$ag_last) {
            return $result;
        }

        foreach ($result as $value) {
            foreach ($value as $c => $v) {
                if ($v->getMethodTitle() == $ag_title) {
                    $scheduled = $v;
                    $scheduledIndex = $c ? $c : 0;
                }
            }
        }

        if ($scheduled) {
            unset($result['intelipost'][$scheduledIndex]);
            array_push($result['intelipost'], $scheduled);
        }

        return $result;
    }
}
