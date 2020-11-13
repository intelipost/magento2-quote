<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Controller\Schedule;

class Index extends \Magento\Framework\App\Action\Action
{
    const COOKIE_NAME = 'scheduled_option';
    const COOKIE_DURATION = 1800; // lifetime in seconds

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    protected $_helper;
    protected $_quoteFactory;
    protected $_checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Intelipost\Quote\Helper\Data $helper,
        \Intelipost\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->_helper = $helper;
        $this->_quoteFactory = $quoteFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_checkoutSession = $checkoutSession;

        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $session = $this->_checkoutSession;

        $quoteId = $this->getRequest()->getParam('quoteId');
        $methodId = $this->getRequest()->getParam('methodId');
        $selDate = $this->getRequest()->getParam('selDate');
        $period = $this->getRequest()->getParam('period');

        // Check
        if (empty($quoteId) || empty($methodId) || empty($selDate) || empty($period)) {
            return false;
        }
        if (intval($quoteId) < 1 || !strtotime($selDate)) {
            return false;
        }
        /*
            $quoteItem = $this->_quoteFactory->create()->load($quoteId);
            if (!$quoteItem->getId()) return false;
        */
        $quoteItem = null;

        foreach ($this->_helper->getResultQuotes() as $quote) {
            if ($quote->getQuoteId() == $quoteId && !strcmp($quote->getDeliveryMethodId(), $methodId)) {
                $quoteItem = $quote;

                break;
            }
        }

        if (empty($quoteItem)) {
            return null;
        }

        // save
        $timestamp = strtotime($selDate);
        $selDate = date('d/m/Y', $timestamp);
        $quoteItem->setSelectedSchedulingDates($selDate);
        $quoteItem->setSelectedSchedulingPeriod($period);
        // $quoteItem->save();

        $session->setIpSelDate($selDate);
        $session->setIpPeriod($period);
        $session->setIpScheludedMethodId($quoteItem->getDeliveryMethodId());

        if ($this->_cookieManager->getCookie(self::COOKIE_NAME)) {
            $this->_cookieManager->deleteCookie(self::COOKIE_NAME);
        }

        $cookie_values = $quoteItem->getDeliveryMethodId() . '+' . $selDate . '+' . $period;

        $metadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(self::COOKIE_DURATION)
            ->setPath('/');

        $this->_cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            $cookie_values,
            $metadata
        );

        $resultPage = $this->_resultPageFactory->create();
        $this->getResponse()->setBody(
            __('Delivery Scheduled for: %1 period: %2', $selDate, __(ucfirst($period)))
        );
    }
}
