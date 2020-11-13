<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Controller\Product;

class Shipping extends \Magento\Framework\App\Action\Action
{
    protected $_quote;

    protected $_resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_quote = $quote;
        $this->_resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $country = $this->getRequest()->getParam('country');
        $postcode = $this->getRequest()->getParam('postcode');
        $productId = $this->getRequest()->getParam('product');
        $qty = $this->getRequest()->getParam('qty');

        $this->_quote->getShippingAddress()
            ->setCountryId($country)
            ->setPostcode($postcode)
            ->setCollectShippingRates(true);

        $product = $this->getProductById($productId);

        $options = new \Magento\Framework\DataObject();
        $options->setProduct($product->getId());
        $options->setQty($qty);

        if (!strcmp($product->getTypeId(), 'configurable')) {
            $superAttribute = $this->getRequest()->getParam('super_attribute');

            $options->setSuperAttribute($superAttribute);
        } elseif (!strcmp($product->getTypeId(), 'bundle')) {
            $bundleOption = $this->getRequest()->getParam('bundle_option');
            $bundleOptionQty = $this->getRequest()->getParam('bundle_option_qty');

            $options->setBundleOption($bundleOption);
            $options->setBundleOptionQty($bundleOptionQty);
        }

        $result = $this->_quote->addProduct($product, $options);
        if (empty($result)) {
            die(__($result));
        }

        $this->_quote->collectTotals();
        $result = $this->_quote->getShippingAddress()->getGroupedAllShippingRates();
        if (is_string($result)) {
            die($result);
        }

        $resultPage = $this->_resultPageFactory->create();
        $this->getResponse()->setBody(
            $resultPage->getLayout()
                ->createBlock('Magento\Framework\View\Element\Template')
                ->setRates($result)
                ->setTemplate('Intelipost_Quote::product/view/result.phtml')
                ->toHtml()
        );
    }

    public function getProductById($id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productManager = $objectManager->create('Magento\Catalog\Model\Product');

        return $productManager->load($id);
    }
}
