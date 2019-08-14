<?php

/**
 * Rybons Coupon Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_couponDiscountIncludesTax;
    protected $_creditPriceIncludesTax;

    public function isEnabled() {
        return true;
    }

    public function couponDiscountIncludesTax($store = null) {
        $storeId = Mage::app()->getStore($store)->getId();
        if (!isset($this->_couponDiscountIncludesTax[$storeId])) {
            $this->_creditPriceIncludesTax[$storeId] = (int) Mage::getStoreConfig(
                            PanthelopeTechnologies_RybonsCoupon_Model_Quote_Total_Tax::CONFIG_XML_PATH_GIFTDISCOUNT_INCLUDES_TAX, $storeId
            );
        }
        return $this->_couponDiscountIncludesTax[$storeId];
    }

    public function getRybonsCouponDiscountTax($price, $includingTax = null, $shippingAddress = null, $ctc = null, $store = null) {
        $billingAddress = false;
        if ($shippingAddress && $shippingAddress->getQuote() && $shippingAddress->getQuote()->getBillingAddress()) {
            $billingAddress = $shippingAddress->getQuote()->getBillingAddress();
        }

        $calc = Mage::getSingleton('tax/calculation');
        $taxRequest = $calc->getRateRequest(
                $shippingAddress, $billingAddress, $shippingAddress->getQuote()->getCustomerTaxClassId(), $store
        );

        $taxRequest->setProductClassId($this->getRybonsCouponDiscountTaxClass($store));
        $rate = $calc->getRate($taxRequest);
        $tax = $calc->calcTaxAmount($price, $rate, $this->couponDiscountIncludesTax($store), true);
        return $tax;
    }

    public function getRybonsCouponDiscountTaxClass($store) {
        return (int) Mage::getStoreConfig(
                        PanthelopeTechnologies_RybonsCoupon_Model_Quote_Total_Tax::CONFIG_XML_PATH_GIFTDISCOUNT_TAX_CLASS, $store
        );
    }

}
