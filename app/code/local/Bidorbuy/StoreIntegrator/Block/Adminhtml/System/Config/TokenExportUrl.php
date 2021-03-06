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

class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_TokenExportUrl
    extends Mage_Adminhtml_Block_System_Config_Form_Field {

    /**
     * Export field
     * 
     * @param Varien_Data_Form_Element_Abstract $element element
     *
     * @return String
     * 
     * @codingStandardsIgnoreStart
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        //@codingStandardsIgnoreEnd
        
        $tokenExport = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getTokenExport();
        $url = Mage::getUrl('bidorbuystoreintegrator/index/export', array(
            '_store' => 'default',
                bobsi\Settings::paramToken => $tokenExport)
        );

        return '
            <input type="hidden" name="groups[links][fields][tokenExportUrl][value]" 
            id="bidorbuystoreintegrator_links_tokenExportUrl"
                value="' . $tokenExport . '">
            <input type="text" readonly="readonly" name="tokenExportUrl" id="tokenExportUrl" value="' . $url . '" />

            <button type="button" class="button launch-button">' . $this->__('Launch') . '</button>
            <button type="button" class="button copy-button">' . $this->__('Copy') . '</button>';
    }
}