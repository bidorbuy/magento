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
class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_ExcludeCategories
    extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /*
    * Set template
    */
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('bidorbuy/storeintegrator/categories.phtml');
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        return $this->_toHtml();
    }
}