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

class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_TokenDownloadUrl extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $tokenDownload = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getTokenDownload();
        $url = Mage::getUrl('bidorbuystoreintegrator/index/download', array('_store' => 'default', bobsi\Settings::paramToken => $tokenDownload));

        return '
            <input type="hidden" readonly="readonly" name="groups[links][fields][tokenDownloadUrl][value]" id="bidorbuystoreintegrator_links_tokenDownloadUrl"
                value="' . $tokenDownload . '" />
            <input type="text" readonly="readonly" name="tokenDownloadUrl" id="tokenDownloadUrl" value="' . $url . '" />

            <button type="button" class="button launch-button">' . $this->__('Launch') . '</button>
            <button type="button" class="button copy-button">' . $this->__('Copy') . '</button>';
    }
}