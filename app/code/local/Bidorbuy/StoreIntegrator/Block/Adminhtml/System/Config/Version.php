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

class Bidorbuy_StoreIntegrator_Block_Adminhtml_System_Config_Version extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getFooterHtml($element) {
        $tokenDownload = bobsi\StaticHolder::getBidorbuyStoreIntegrator()->getSettings()->getTokenDownload();
        $url = Mage::getUrl('bidorbuystoreintegrator/index/version',
            array(
                '_store' => 'default',
                bobsi\Settings::paramToken => $tokenDownload,
                'phpinfo'=>'y'
            ));

        $phpInfo = "<a href=\"$url\" target='_blank'>@See PHP information</a>";
        $html = '</tbody></table>';

        $html .= bobsi\Version::getLivePluginVersion();

        if ($element->getIsNested()) {
            $html .= '</div></td></tr>';
        } else {
            $html .= '</div>';
        }
        return $phpInfo.$html;
    }
}