<?php

/**
 * Giftcard API Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Block_Sales_Order_Totals_Discount extends Mage_Core_Block_Abstract {

    public function initTotals() {
        $_order = $this->getParentBlock()->getOrder();
        $couponDiscountAmount = $this->getParentBlock()->getSource()->getRybonsCouponDiscount();
        $couponDiscountTaxAmount = $this->getParentBlock()->getSource()->getRybonsCouponDiscountTax();

        if ($couponDiscountAmount > 0) {

            $giftcardDiscountTotal = new Varien_Object(array(
                'code' => 'RybonsCoupon_discount',
                'label' => $this->__('Rybons Coupon Discount'),
                'value' => -$couponDiscountAmount,
            ));
            $this->getParentBlock()->addTotalBefore($giftcardDiscountTotal, 'grand_total');
        }
        return $this;
    }

}
