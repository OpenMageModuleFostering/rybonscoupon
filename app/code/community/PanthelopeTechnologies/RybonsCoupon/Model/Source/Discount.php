<?php

/**
 * Rybons Coupon Extension
 * @category   Magento Extensions
 * @package    PanthelopeTechnologies_RybonsCoupon
 * @author     Pathelope Technologies <info@panthelope.com>
 */
class PanthelopeTechnologies_RybonsCoupon_Model_Source_Discount {

    const FIXED_TYPE = '1';
    const PERCENTAGE_TYPE = '2';

    /*
     * Returns array of style options
     * @return array Options array like id => name
     */

    public static function toShortOptionArray() {
        $helper = Mage::helper('RybonsCoupon');
        $result = array(
            self::FIXED_TYPE => $helper->__('Fixed'),
            self::PERCENTAGE_TYPE => $helper->__('Percentage')
        );
        return $result;
    }

    public static function toOptionArray() {
        $options = self::toShortOptionArray();
        $res = array();

        foreach ($options as $k => $v)
            $res[] = array(
                'value' => $k,
                'label' => $v
            );

        return $res;
    }

}
