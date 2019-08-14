<?php

/**
 * Giftcard API Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Block_Sales_Invoice_Totals_Discount extends Mage_Core_Block_Abstract {

    public function initTotals() {
        $parent = $this->getParentBlock();
        $this->_invoice = Mage_Adminhtml_Block_Sales_Items_Abstract::getInvoice();
        $couponDiscountAmount = $this->_invoice->getRybonsCouponDiscount();
        $giftcardDiscounttTaxAmount = $this->_invoice->getRybonsCouponDiscountTax();

        if ($couponDiscountAmount > 0) {

            $giftcardDiscountTotal = new Varien_Object(array(
                'code' => 'RybonsCoupon_discount',
                'label' => $this->__('Rybons Coupon Discount'),
                'value' => -$couponDiscountAmount,
            ));
            $parent->addTotalBefore($giftcardDiscountTotal, 'tax');
        }
        return $this;
    }

}
