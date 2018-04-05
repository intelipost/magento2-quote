<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Block\Product;

class View extends \Magento\Catalog\Block\Product\View
{

protected $_intelipostHelper;

public function __construct(
    \Magento\Catalog\Block\Product\Context $context,
    \Magento\Framework\Url\EncoderInterface $urlEncoder,
    \Magento\Framework\Json\EncoderInterface $jsonEncoder,
    \Magento\Framework\Stdlib\StringUtils $string,
    \Magento\Catalog\Helper\Product $productHelper,
    \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
    \Magento\Framework\Locale\FormatInterface $localeFormat,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
    \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
    \Intelipost\Quote\Helper\Data $intelipostHelper,
    array $data = []
)
{
    $this->_intelipostHelper = $intelipostHelper;

    parent::__construct(
        $context,
        $urlEncoder,
        $jsonEncoder,
        $string,
        $productHelper,
        $productTypeConfig,
        $localeFormat,
        $customerSession,
        $productRepository,
        $priceCurrency,
        $data
    );
}

public function getAjaxShippingUrl()
{
    return $this->getUrl('intelipost_quote/product/shipping');
}

public function getProduct()
{
    $product = $this->_coreRegistry->registry('current_product');

    return $product;
}

public function getCurrentProductUrl ()
{
    $result = $this->_intelipostHelper->getCurrentUrl ();

    return $result;
}

}

