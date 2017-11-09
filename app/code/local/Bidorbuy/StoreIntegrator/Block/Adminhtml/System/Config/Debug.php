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
 * Class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_Debug
 * @deprecated after feature 3910
 */
class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_Debug
    extends Mage_Adminhtml_Block_System_Config_Form_Field {

    /**
     * BAA fieldset
     *
     * @param  Varien_Data_Form_Element_Abstract $element element
     *                                                    
     * @return String
     */
    protected function _getElementHtml( Varien_Data_Form_Element_Abstract $element ) {
        $html = ' 
             <tr>
                <td colspan="5" class="label"><b>Basic Access Authentication</b><br>(if necessary)</td>
             <tr>
                 <td colspan="5" class="label" style="width: 100%" >
                     <span style="color: red"> 
                        Do not enter username or password of ecommerce platform, 
                        please read carefully about this kind of authentication!
                    </span>
                 </td>
             </tr>';


        return $html;
    }
}