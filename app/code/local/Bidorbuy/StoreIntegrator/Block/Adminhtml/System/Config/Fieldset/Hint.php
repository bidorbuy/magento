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
class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_Fieldset_Hint extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface {
    //@codingStandardsIgnoreStart
    protected $_template = 'bidorbuy/system/config/fieldset/hint.phtml';
    //@codingStandardsIgnoreEnd

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        return $this->toHtml();
    }
}