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

require_once(Mage::getModuleDir('', 'Bidorbuy_StoreIntegrator') . DS . 'Helper' . DS . 'Bidorbuy.php');

use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

/**
 * Used in creating options for Compress Library config value selection.
 */
class Bidorbuy_StoreIntegrator_Model_System_Config_Source_LoggingLevel extends bobsi\Settings {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        $arrayToReturn = array();
        foreach ($this->getLoggingLevelOptions() as $option)
            $arrayToReturn[] = array('value' => $option, 'label' => Mage::helper('adminhtml')->__(ucfirst($option)));

        return $arrayToReturn;
    }
}
