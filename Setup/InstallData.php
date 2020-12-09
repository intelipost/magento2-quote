<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $_eavSetupFactory;
    protected $_scopeConfig;

    protected $_attributesList = [];
    protected $_import;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Intelipost\Quote\Model\Import $import
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_import = $import;
        $this->_attributesList = [
            'height' => __('Height'),
            'width' => __('Width'),
            'length' => __('Length')
        ];
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        foreach ($this->_attributesList as $attributeCode => $attributeName) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'intelipost_product_' . $attributeCode,
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => __('Intelipost Product ' . $attributeName),
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }

        $requestTable = $this->_scopeConfig->getValue('carriers/intelipost/contingency_table');

        $this->_import->import($requestTable);

        $setup->endSetup();
    }

}
