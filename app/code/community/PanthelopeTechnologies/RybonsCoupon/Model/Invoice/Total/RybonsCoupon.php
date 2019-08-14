<?php

/**
 * Rybons Coupon Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Model_Invoice_Total_RybonsCoupon extends Mage_Sales_Model_Order_Invoice_Total_Abstract {

    public function collect(Mage_Sales_Model_Order_Invoice $invoice) {

        $order = $invoice->getOrder();

        if (!$order->getBaseRybonsCouponDiscount() || $order->getBaseRybonsCouponDiscount() == $order->getBaseRybonsCouponDiscountInvoiced()) {
            return $this;
        }

        $baseRybonsCouponDiscount = $order->getBaseRybonsCouponDiscount();
        $baseRybonsCouponDiscountInvoiced = floatval($order->getBaseRybonsCouponDiscountInvoiced());
        $baseInvoiceTotal = $invoice->getBaseGrandTotal();
        $giftcardDiscount = $order->getRybonsCouponDiscount();
        $giftcardDiscountInvoiced = floatval($order->getRybonsCouponDiscountInvoiced());
        $invoiceTotal = $invoice->getGrandTotal();

        if (!$baseRybonsCouponDiscount || $baseRybonsCouponDiscountInvoiced == $baseRybonsCouponDiscount) {
            return $this;
        }

        $baseRybonsCouponDiscountToInvoice = $baseRybonsCouponDiscount - $baseRybonsCouponDiscountInvoiced;
        $giftcardDiscountToInvoice = $giftcardDiscount - $giftcardDiscountInvoiced;
        $giftcardDiscountTax = $invoice->getOrder()->getRybonsCouponDiscountTax();
        $baseRybonsCouponDiscountTax = $invoice->getOrder()->getBaseRybonsCouponDiscountTax();
        $baseInvoiceTotal = $baseInvoiceTotal - $baseRybonsCouponDiscountToInvoice;
        $invoiceTotal = $invoiceTotal - $giftcardDiscountToInvoice;
        $invoice->setBaseGrandTotal($baseInvoiceTotal);
        $invoice->setGrandTotal($invoiceTotal);
        $invoice->setBaseRybonsCouponDiscount($baseRybonsCouponDiscountToInvoice);
        $invoice->setRybonsCouponDiscount($giftcardDiscountToInvoice);

        $order->setBaseRybonsCouponDiscountInvoiced($baseRybonsCouponDiscountInvoiced + $baseRybonsCouponDiscountToInvoice);
        $order->setRybonsCouponDiscountInvoiced($giftcardDiscountInvoiced + $giftcardDiscountToInvoice);

        return $this;
    }

}
