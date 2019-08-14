<?php

/**
 * Rybons Coupon Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Adminhtml_ExtendoController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
