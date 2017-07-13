<?php

/**
 * Copyright (c) 2014, 2015, 2016 Bidorbuy http://www.bidorbuy.co.za
 * This software is the proprietary information of Bidorbuy.
 *
 * All Rights Reserved.
 * Modification, redistribution and use in source and binary forms, with or without modification
 * are not permitted without prior written approval by the copyright holder.
 *
 * Vendor: EXTREME IDEA LLC http://www.extreme-idea.com
 */

/**
 * Used in creating options for Compress Library config value selection.
 */
class Bidorbuy_StoreIntegrator_Model_System_Config_Source_OnOff {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('On')),
            array('value' => 0, 'label' => Mage::helper('adminhtml')->__('Off')),
        );
    }
}
