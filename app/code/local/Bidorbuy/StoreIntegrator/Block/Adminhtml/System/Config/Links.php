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

use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_Links extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getFooterHtml($element) {
        $tooltipsExist = false;
        $html = '</tbody></table>';
        $html .= '<input type="hidden" 
		    name="' . bobsi\Settings::nameActionReset . '"
		    id="' . bobsi\Settings::nameActionReset . '"
		    value="0">';
        $html .= $this->_getResetTokensButtonHtml();

        $html .= '</fieldset>' . $this->_getExtraJs($element, $tooltipsExist);

        if ($element->getIsNested()) {
            $html .= '</div></td></tr>';
        } else {
            $html .= '</div>';
        }
        return $html;
    }

    protected function _getResetTokensButtonHtml() {
        $buttonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('submit')
            ->setLabel($this->__($this->__('Reset Tokens')))
            ->setOnClick("$(" . bobsi\Settings::nameActionReset . ").value = 1;")
            ->toHtml();

        return $buttonHtml;
    }
}