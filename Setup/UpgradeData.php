<?php
/*
 * @package     Intelipost_Quote
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Quote\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $_eavSetupFactory;
    protected $_scopeConfig;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'intelipost_category_attribute',
                [
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => __('Intelipost Category Attribute'),
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

        $setup->endSetup();
    }
}
