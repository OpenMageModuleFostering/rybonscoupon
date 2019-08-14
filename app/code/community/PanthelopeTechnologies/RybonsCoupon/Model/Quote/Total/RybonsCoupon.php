<?php

/**
 * Rybons Coupon Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Model_Quote_Total_RybonsCoupon extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    public function __construct() {
        $this->setCode('RybonsCoupon');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address) {
        if (Mage::app()->getStore()->isAdmin()) {
            $allItems = Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->getAllItems();
            $productIds = array();
            foreach ($allItems as $item) {
                $productIds[] = $item->getProductId();
            }
        } else {
            $productIds = Mage::getSingleton('checkout/cart')->getProductIds();
        }

        if (count($productIds) == 0)
            return $this;

        $addressType = Mage_Sales_Model_Quote_Address::TYPE_BILLING;
        foreach ($productIds as $productId) {
            $productTypeId = Mage::getModel('catalog/product')->load($productId)->getTypeId();
            if ($productTypeId != 'downloadable' && $productTypeId != 'virtual') {
                $addressType = Mage_Sales_Model_Quote_Address::TYPE_SHIPPING;
                break;
            }
        }

        //shipping or billing
        if ($addressType != $address->getAddressType())
            return $this;
        $discountAmount = 0;
        $address->setRybonsCouponDiscount(0);
        $address->setBaseRybonsCouponDiscount(0);
        $address->setRybonsCouponDiscountTax(0);
        $address->setBaseRybonsCouponDiscountTax(0);
        $session = Mage::getSingleton('checkout/session');
        if (!$session->getUseRybonsCouponDiscount())
            return $this;
        $orderData = Mage::app()->getRequest()->getPost('order');
        if (Mage::getModel('checkout/cart')->getQuote()->getData('items_qty') == 0 && !Mage::getSingleton('adminhtml/session_quote')->getCustomerId()) {
            return $this;
        }

        $quote = $address->getQuote();

        $baseGrandTotal = floatval($address->getBaseGrandTotal());
        $grandTotal = floatval($address->getGrandTotal());
        $baseShipping = floatval($address->getBaseShippingInclTax());
        $shipping = floatval($address->getShippingInclTax());
        if ($baseGrandTotal)
            $baseSubtotal = $baseGrandTotal - $baseShipping;
        else
            $baseSubtotal = floatval($address->getBaseSubtotalInclTax());
        if ($grandTotal)
            $subtotal = $grandTotal - $shipping;
        else
            $subtotal = floatval($address->getSubtotalInclTax());

        $quote = $address->getQuote();
        $store = $quote->getStore();
        $quoteTotal = $baseSubtotal + $baseShipping;
        $giftcardDiscount = array();
        $giftcardDiscount[] = $session->getRybonsCouponDiscountAmount();
        sort($giftcardDiscount, SORT_NUMERIC);
        $discountAmount = $giftcardDiscount[0];

        if ((int) $discountAmount >= (int) $address->getBaseGrandTotal())
            $discountAmount = $address->getBaseGrandTotal();
        $baseTax = floatval($address->getBaseTaxAmount());
        $tax = floatval($address->getTaxAmount());

        $taxAmount = Mage::helper('RybonsCoupon')->getRybonsCouponDiscountTax(
                $discountAmount, true, $address, $address->getQuote()->getCustomerTaxClassId(), $store
        );
        $address->setBaseRybonsCouponDiscount($discountAmount);
        $address->setRybonsCouponDiscount($store->convertPrice($discountAmount, false));

        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $discountAmount);
        $address->setGrandTotal($address->getGrandTotal() - $store->convertPrice($discountAmount, false));

        $address->setBaseRybonsCouponDiscountTax($taxAmount);
        $address->setRybonsCouponDiscountTax($store->convertPrice($taxAmount, false));
        $session->setUseRybonsCouponDiscount(true);

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        if (!Mage::helper('RybonsCoupon')->isEnabled())
            return $this;

        if ($address->getRybonsCouponDiscount() > 0) {
            $quote = $address->getQuote();
            $store = $quote->getStore();
            $amount = $address->getRybonsCouponDiscount();

            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('RybonsCoupon')->__('RYBONS COUPON'),
                'value' => -$amount,
            ));
        }
    }

}
