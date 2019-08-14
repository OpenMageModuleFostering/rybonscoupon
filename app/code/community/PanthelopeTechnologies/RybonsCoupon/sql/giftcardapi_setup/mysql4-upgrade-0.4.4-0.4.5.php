<?php

$installer = $this;
$installer->startSetup();
$setup = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$setup->addAttribute('quote', 'RybonsCoupon_code', array('type' => 'varchar'));
$setup->addAttribute('order', 'RybonsCoupon_code', array('type' => 'varchar'));
$setup->addAttribute('invoice', 'RybonsCoupon_code', array('type' => 'varchar'));
$setup->addAttribute('quote', 'RybonsCoupon_discount', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('quote', 'base_RybonsCoupon_discount', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('order', 'RybonsCoupon_discount', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('order', 'base_RybonsCoupon_discount', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('order', 'RybonsCoupon_discount_invoiced', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('order', 'base_RybonsCoupon_discount_invoiced', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('order', 'RybonsCoupon_discount_tax', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('order', 'base_RybonsCoupon_discount_tax', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('order', 'RybonsCoupon_discount_tax_invoiced', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('order', 'base_RybonsCoupon_discount_tax_invoiced', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('invoice', 'RybonsCoupon_discount', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('invoice', 'base_RybonsCoupon_discount', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('invoice', 'RybonsCoupon_discount_tax', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('invoice', 'base_RybonsCoupon_discount_tax', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('creditmemo', 'RybonsCoupon_discount', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('creditmemo', 'base_RybonsCoupon_discount', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('creditmemo', 'RybonsCoupon_discount_tax', array('type' => 'decimal', 'visible' => false));
$setup->addAttribute('creditmemo', 'base_RybonsCoupon_discount_tax', array('type' => 'decimal', 'visible' => false));

$installer->endSetup();
