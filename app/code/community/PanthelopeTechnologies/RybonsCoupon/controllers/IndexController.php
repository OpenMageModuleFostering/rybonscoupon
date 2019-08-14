<?php

/**
 * Rybons Coupon Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_IndexController extends Mage_Core_Controller_Front_Action {

    protected function _getSession() {
        return Mage::getSingleton('checkout/session');
    }

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function reorderAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
    }

    public function getRybonsCouponAction() {
        $data = $this->getRequest()->getPost();
        if ($data) {
            if ($data['submitAction'] == "remove") {
                $this->_getSession()->getQuote()->setRybonsCouponCode('')->save();
                $this->_getSession()->setUseRybonsCouponDiscount(false);
                $this->_getSession()->setRybonsCouponDiscountAmount(0);
                $this->_getSession()->setAppliedRybonsCouponCode("");
                $this->_getSession()->addSuccess($this->__('Rybons coupon has been removed.'));
                $this->_redirect('checkout/cart');
                return;
            }
            if ($data['coupon'] == '') {
                $this->_getSession()->addError($this->__('Please enter a Rybons coupon code.'));
                $this->_redirect('checkout/cart');
                return;
            }


            $array_config = Mage::getStoreConfig('RybonsCoupon/PanthelopeTechnologies_group', Mage::app()->getStore());
            $merchantKey = $array_config['PanthelopeTechnologies_merchantKey'];
            $mode = $array_config['PanthelopeTechnologies_connectionMode'];
            $coupon_code = $data['coupon'];
            //Mage::log("Mkey: {$merchantKey} | Mode: {$mode} | Coupon: {$coupon_code} | SubTotal: {$this->_getSession()->getQuote()->getSubtotal()}", null, 'rybons.log');

            $url = "http://rybons.com.ng/webservice/coupons/get.json";
            if ($mode == "1") {
                $url = "http://rybons.com.ng/webservice/coupons/get.json";
            }

            $postFields = "?coupon_code={$coupon_code}&merchant_key={$merchantKey}";
            $url .= $postFields;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => 'RYBONS Magento Extension'
            ));
            $result = curl_exec($curl);
            curl_close($curl);

            //Mage::log($result, null, 'rybons.log');
            if ($result != false) {
                $response = json_decode($result, true);
                if (isset($response["PayLoad"]["status"]) && $response["PayLoad"]["status"]) {
                    $couponResponse = $response["PayLoad"]["data"]["Coupon"];
                    $error = "";
                    $couponValue = $this->getRybonsCouponValue($couponResponse, $error);
                    if ($couponValue > 0) {
                        $this->_getSession()->setAppliedRybonsCouponCode($coupon_code);
                        $this->_getSession()->getQuote()->setRybonsCouponCode($coupon_code)->save();
                        $this->_getSession()->addSuccess($this->__('Your Rybons Coupon has been applied.'));
                        $this->_getSession()->setUseRybonsCouponDiscount(true);
                        $this->_getSession()->setRybonsCouponDiscountAmount($couponValue);
                    } else {
                        $this->_getSession()->setAppliedRybonsCouponCode("");
                        $this->_getSession()->getQuote()->setRybonsCouponCode('')->save();
                        $this->_getSession()->setUseRybonsCouponDiscount(false);
                        $this->_getSession()->setRybonsCouponDiscountAmount(0);
                        $this->_getSession()->addError($this->__($error));
                    }
                } else {
                    $this->_getSession()->setAppliedRybonsCouponCode("");
                    $this->_getSession()->getQuote()->setRybonsCouponCode('')->save();
                    $this->_getSession()->setUseRybonsCouponDiscount(false);
                    $this->_getSession()->setRybonsCouponDiscountAmount(0);
                    $this->_getSession()->addError($this->__($response["PayLoad"]["data"]));
                }
            }
        }
        $this->_redirect('checkout/cart');
    }

    private function getRybonsCouponValue($couponResponse, &$error = null) {
        if ($this->_getSession()->getQuote()->getSubtotal() > $couponResponse['minimum_purchase_value']) {
            return $couponResponse["percentage_coupon_value"] > 0 ? (($couponResponse["percentage_coupon_value"] / 100) * $this->_getSession()->getQuote()->getSubtotal()) : $couponResponse['flat_coupon_value'];
        } else {
            $error = "A minimum purchase amount of N" . number_format($couponResponse['minimum_purchase_value'], 2) . " is required.";
        }
        return 0.00;
    }

}
