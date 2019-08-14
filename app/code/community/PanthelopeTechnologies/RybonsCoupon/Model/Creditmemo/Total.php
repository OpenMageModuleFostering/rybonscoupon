<?php

/**
 * Rybons Coupon Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Model_Creditmemo_Total extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract {

    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo) {

        $order = $creditmemo->getOrder();

        $baseCreditmemoTotal = $creditmemo->getBaseGrandTotal();
        $creditmemoTotal = $creditmemo->getGrandTotal();
        $baseRybonsCouponDiscountInvoiced = $order->getBaseRybonsCouponDiscountInvoiced();
        $RybonsCouponDiscountInvoiced = $order->getRybonsCouponDiscountInvoiced();

        if ($creditmemo->getInvoice()) {
            $invoice = $creditmemo->getInvoice();
            $baseRybonsCouponDiscountToCredit = $invoice->getBaseRybonsCouponDiscount();
            $RybonsCouponDiscountToCredit = $invoice->getRybonsCouponDiscount();
        } else {
            $baseRybonsCouponDiscountToCredit = $baseRybonsCouponDiscountInvoiced;
            $RybonsCouponDiscountToCredit = $RybonsCouponDiscountInvoiced;
        }

        if (!$baseRybonsCouponDiscountToCredit > 0) {
            return $this;
        }

        $creditmemo->setBaseGrandTotal($baseCreditmemoTotal - $baseRybonsCouponDiscountToCredit);
        $creditmemo->setGrandTotal($creditmemoTotal - $RybonsCouponDiscountToCredit);

        $creditmemo->setBaseRybonsCouponDiscount($baseRybonsCouponDiscountToCredit);
        $creditmemo->setRybonsCouponDiscount($RybonsCouponDiscountToCredit);

        return $this;
    }

}
