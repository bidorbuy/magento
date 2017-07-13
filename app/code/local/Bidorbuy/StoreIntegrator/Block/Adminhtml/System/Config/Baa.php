<?php

/**
 * Copyright (c) 2014, 2015, 2016 Bidorbuy http://www.bidorbuy.co.za
 * This software is the proprietary information of Bidorbuy.
 *
 * All Rights Reserved.
 * Modification, redistribution and use in source and binary forms, with or without
 * modification are not permitted without prior written approval by the copyright
 * holder.
 *
 * Vendor: EXTREME IDEA LLC http://www.extreme-idea.com
 */

require_once(Mage::getModuleDir('', 'Bidorbuy_StoreIntegrator') . DS . 'Helper' . DS . 'Bidorbuy.php');
use com\extremeidea\bidorbuy\storeintegrator\core as bobsi;

class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_Baa extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        $userName = Mage::getStoreConfig('bidorbuystoreintegrator/debug/username');
        $password = Mage::getStoreConfig('bidorbuystoreintegrator/debug/password');
        $html = "
             <tr>
                <td colspan='5' class='label'><b>Basic Access Authentication</b><br>(if necessary)</td>
             </tr>
             <tr>
                 <td colspan='5' class='label' style='width: 100%' >
                     <span style='color: red'> 
                        Do not enter username or password of ecommerce platform, 
                        please read carefully about this kind of authentication!
                    </span>
                 </td>
             </tr>
             
            <tr id='row_bidorbuystoreintegrator_debug_username'>
                <td class='label'><label for='bidorbuystoreintegrator_debug_username'> Username</label></td>
                <td class='value'><input id='bidorbuystoreintegrator_debug_username' name='groups[debug][fields][username][value]' class=' input-text' type='text' value='$userName'>
                    <p class='note'>
                        <span>
                          Please specify the username if your platform is protected by <a href='http://en.wikipedia.org/wiki/Basic_access_authentication' target='_blank'>Basic Access Authentication</a>
                     </span>
                 </p>
            </td>
            </tr>
                            
            <tr id='row_bidorbuystoreintegrator_debug_password'>
                <td class='label'><label for='bidorbuystoreintegrator_debug_password'> Password</label></td>
                <td class='value'><input id='bidorbuystoreintegrator_debug_password' name='groups[debug][fields][password][value]'  class=' validate-password input-text' type='password' value='$password'>
                   <p class='note'>
                       <span>
                           Please specify the password if your platform is protected by <a href='http://en.wikipedia.org/wiki/Basic_access_authentication' target='_blank'>Basic Access Authentication</a>
                       </span>
                   </p>
                </td>
            </tr>         
             ";

        return $this->getRequest()->getParam('baa', false) ? $html : '';
    }
}