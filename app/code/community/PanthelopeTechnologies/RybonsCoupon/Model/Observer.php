<?php

/**
 * Rybons Coupon Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Model_Observer {

    public function salesOrderSaveBefore($observer) {
        $order = $observer->getEvent()->getOrder();
        $coupon = Mage::getSingleton('checkout/session')->getAppliedRybonsCouponCode();
        $order->setRybonsCouponCode($coupon);
        //Mage::log("salesOrderSaveBefore | {$coupon}", null, 'rybons.log');
    }

    public function salesOrderSaveAfter($observer) {
        $order = $observer->getEvent()->getOrder();
        $coupon_code = $order->getRybonsCouponCode();
        //Mage::log("salesOrderSaveAfter | $coupon_code", null, 'rybons.log');
        if ($coupon_code) {
            $array_config = Mage::getStoreConfig('RybonsCoupon/PanthelopeTechnologies_group', Mage::app()->getStore());
            $merchantKey = $array_config['PanthelopeTechnologies_merchantKey'];
            $mode = $array_config['PanthelopeTechnologies_connectionMode'];

            $order_id = $order->getIncrementId();
            //Mage::log("Mkey: {$merchantKey} | Mode: {$mode} | Coupon: {$coupon_code} | SubTotal: {$order->getSubtotal()} | OrderID: {$order_id}", null, 'rybons.log');

            $details = array();
            $details["customer"] = array(
                "fname" => $order->getBillingAddress()->getFirstname(),
                "lname" => $order->getBillingAddress()->getLastname(),
                "email" => $order->getCustomerEmail(),
                "phone" => $order->getCustomerTelephone(),
            );

            $details['order_id'] = $order_id;
            $details['purchase_amount'] = $order->getSubtotal();

            $url = "http://rybons.com.ng/webservice/coupons/apply.json";
            if ($mode == "1") {
                $url = "http://rybons.com.ng/webservice/coupons/apply.json";
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => 'RYBONS Magento Extension',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => array(
                    "coupon_code" => $coupon_code,
                    "merchant_key" => $merchantKey,
                    "buyer_details" => json_encode($details)
                )
            ));
            $result = curl_exec($curl);
            curl_close($curl);

            if ($result != false) {
                $response = json_decode($result, true);
                if (isset($response["PayLoad"]["status"]) && $response["PayLoad"]["status"]) {
                    //$this->RybonsCoupon_destroySession();
                } else {
                    $controllerAction = $observer->getEvent()->getControllerAction();
                    $result = array();
                    $result['error'] = '-1';
                    $result['message'] = $response["PayLoad"]["data"] != null ? $response["PayLoad"]["data"] : "Unable to apply your Rybons Coupon, it may have been used or expired.";
                    $controllerAction->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    exit;
                }
            } else {
                $controllerAction = $observer->getEvent()->getControllerAction();
                $result = array();
                $result['error'] = '-1';
                $result['message'] = "Unable to apply your Rybons Coupon";
                $controllerAction->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                exit;
            }

            Mage::getSingleton('checkout/session')->setAppliedRybonsCouponCode("");
            Mage::getSingleton('checkout/session')->getQuote()->setRybonsCouponCode('')->save();
            Mage::getSingleton('checkout/session')->setUseRybonsCouponDiscount(false);
            Mage::getSingleton('checkout/session')->setRybonsCouponDiscountAmount(0);
        }
    }

    public function salesInvoiceCreateAfter($observer) {
        
    }

    public function getProductFinalPrice($observer) {
        
    }

    public function insertBlock($observer) {
        $_block = $observer->getBlock();
        $_type = $_block->getType();
        if ($_type == 'checkout/cart_coupon') {
            $_child = clone $_block;
            $_child->setType('RybonsCoupon/discount');
            $_block->setChild('child', $_child);
            $_block->setTemplate('RybonsCoupon/discount.phtml');
        }
    }

}
