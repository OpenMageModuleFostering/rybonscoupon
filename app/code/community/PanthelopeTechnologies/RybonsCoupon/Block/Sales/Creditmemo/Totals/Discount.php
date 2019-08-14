<?php

/**
 * Suregits Giftcard API Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Sodiq<damilolasodiq@gmail.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Block_Sales_Creditmemo_Totals_Discount extends Mage_Core_Block_Abstract {

    public function initTotals() {
        $parent = $this->getParentBlock();
        $this->_creditmemo = Mage_Adminhtml_Block_Sales_Items_Abstract::getCreditmemo();
        $couponDiscountAmount = $this->_creditmemo->getRybonsCouponDiscount();
        $giftcardDiscountTaxAmount = $this->_creditmemo->getRybonsCouponDiscountTax();

        if ($couponDiscountAmount > 0) {

            $couponDiscountTotal = new Varien_Object(array(
                'code' => 'RybonsCoupon_discount',
                'label' => $this->__('Rybons Coupon Discount'),
                'value' => -$couponDiscountAmount,
            ));
            $parent->addTotalBefore($couponDiscountTotal, 'tax');
        }
        return $this;
    }

}
